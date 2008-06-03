<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @see Omeka_Context
 */ 
require_once 'Omeka/Context.php';

/**
 * Core class used to bootstrap the Omeka environment.
 * 
 * Various duties include, but are not limited to, sanitizing magic_quotes,
 * setting up class autoload, database, configuration files, logging, plugins,
 * front controller, etc.
 *
 * This class delegates to the Omeka_Context instance, which holds all state
 * that get initialized by this class.  Methods can be called on this class
 * as though it were an instance of Omeka_Context.
 *
 * @uses Omeka_Context
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Core
{
    /**
     * Array containing all core loading phase methods in sequential order. 
     * Modify this array if any phase is added or deleted.
     *
     * @access protected
     * @var array
     */
    protected $_phases = array('sanitizeMagicQuotes', 
                               'initializeClassLoader', 
                               'initializeConfigFiles', 
                               'initializeLogger', 
                               'initializeDb', 
                               'initializeOptions', 
                               'loadModelClasses', 
                               'initializeAcl', 
                               'initializePluginBroker', 
                               'initializeAuth', 
                               'initializeCurrentUser', 
                               'initializeFrontController');
    
    /**
     * 'Context' is a term that describes a pattern for storing site-wide data
     * in a singleton.  Stuff like the logger, acl, database objects, etc. is 
     * accessible through this object 
     *
     * @var Omeka_Context
     **/
    protected $_context;
    
    /**
     * Initialize a context for this particular application request
     *
     * @return void
     **/
    public function __construct()
    {
        require_once 'globals.php';
        $this->_context = Omeka_Context::getInstance();
    }
    
    /**
     * Delegate to the context object
     *
     * @return mixed
     **/
    public function __call($m, $a)
    {
        return call_user_func_array(array($this->_context, $m), $a);
    }
    
    public function getContext()
    {
        return $this->_context;
    }
    
    /**
     * If magic_quotes has been enabled, then strip all slashes from the $_GET, 
     * $_POST and $_REQUEST superglobals.
     * 
     * @return void
     **/
    public function sanitizeMagicQuotes()
    {
        //Strip out those bastard slashes
        if (get_magic_quotes_gpc()) {
            $_POST    = stripslashes_deep($_POST);
            $_REQUEST = stripslashes_deep($_REQUEST);
            $_GET     = stripslashes_deep($_GET);
        }
    }
    
    /**
     * Bootstrap the entire application.
     *
     * This will initialize all the elements of the application.
     * 
     * @return void
     **/
    public function initialize()
    {
        foreach ($this->_phases as $phase) {
            if (method_exists($this, $phase)) {
                $this->$phase();
            } else {
                exit("Error: The core initialize phase method \"$phase\" does not exist.");
            }
        }
    }
    
    /**
     * Provide phased loading of core Omeka functionality. Primarily used for 
     * Omeka scripts that run outside a web environment.
     *
     * @param string $stopPhase The phase where the user wants loading to stop. 
     * @return void
     **/
    public function phasedLoading($stopPhase)
    {
        // Throw an error if the stop phase doesn't exist.
        if (!in_array($stopPhase, $this->_phases)) {
            exit("Error: The provided stop phase method \"$stopPhase\" does not exist.");
        }
        
        // Load initialization callbacks in the proper order.
        foreach ($this->_phases as $phase) {
            if (method_exists($this, $phase)) {
                call_user_func(array($this, $phase));
                if ($phase == $stopPhase) {
                    break;
                }
            } else {
                exit("Error: The core phase method \"$phase\" does not exist.");
            }
        }
    }
    
    /**
     * Register the autoloader that will load classes based on Zend Framework
     * naming conventions.
     * 
     * @return void
     **/
    public function initializeClassLoader()
    {
        require_once 'Omeka.php';
        spl_autoload_register(array('Omeka', 'autoload'));
    }
    
    /**
     * Initialize the default database connection for all Omeka requests.
     *
     * @uses Omeka_Db
     * @return void
     **/
    public function initializeDb()
    {
        try {
            $db = $this->getConfig('db');
            
            // Fail on improperly configured db.ini file
            if (!isset($db->host) || ($db->host == 'XXXXXXX')) {
                throw new Zend_Config_Exception('Your Omeka database configuration file has not been set up properly.  Please edit the configuration and reload this page.');
            }
            
            $dbh = Zend_Db::factory('Mysqli', array('host'     => $db->host,
                                                    'username' => $db->username,
                                                    'password' => $db->password,
                                                    'dbname'   => $db->name));
        
        // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Db_Adapter_Exception $e) {
            echo $e->getMessage();
            exit;
        
        // perhaps factory() failed to load the specified Adapter class
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
            exit;
        
        } catch (Zend_Config_Exception $e) {
            echo $e->getMessage();
            exit;
        
        } catch (Exception $e) {
            $this->installerNotification();
        }

        $db_obj = new Omeka_Db($dbh, $db->prefix);
                
        $this->setDb($db_obj);   
    }
    
    /**
     * Retrieve all the options from the database.  
     *
     * Options are essentially site-wide variables that are stored in the 
     * database, for example the title of the site.
     * 
     * @return void
     **/
    public function initializeOptions()
    {
        // Pull the options from the DB
        try {
            $db = $this->getDb();
            
            $option_stmt = $db->query("SELECT * FROM $db->Option");
            
            if (!$option_stmt) {
                throw new Exception('Install me!');
            }
            
            $option_array = $option_stmt->fetchAll();
            
            // ****** CHECK TO SEE IF OMEKA IS INSTALLED ****** 
            if (!count($option_array)) {
                throw new Exception('Install me!');
            }
        } catch (Exception $e) {
            $this->installerNotification();
        }
        
        // Save the options so they can be accessed
        $options = array();
        foreach ($option_array as $opt) {
            $options[$opt['name']] = $opt['value'];
        }
        
        $this->setOptions($options);
    }
    
    /**
     * Load the 3 required INI files for Omeka (config.ini, routes.ini and
     * db.ini).
     * 
     * @return void
     **/
    public function initializeConfigFiles()
    {
        require_once 'Zend/Config/Ini.php';
        
        $db_file = CONFIG_DIR . DIRECTORY_SEPARATOR . 'db.ini';
        
        if (!file_exists($db_file)) {
            throw new Zend_Config_Exception('Your Omeka database configuration file is missing.');
        }
        
        if (!is_readable($db_file)) {
            throw new Zend_Config_Exception('Your Omeka database configuration file cannot be read by the application.');
        }
        
        $db = new Zend_Config_Ini($db_file, 'database');
        
        $this->setConfig('db', $db);
        
        $config = new Zend_Config_Ini(CONFIG_DIR . DIRECTORY_SEPARATOR . 'config.ini', 'site');
        
        $this->setConfig('basic', $config);
        
        $routes = new Zend_Config_Ini(CONFIG_DIR . DIRECTORY_SEPARATOR . 'routes.ini', null);
        
        $this->setConfig('routes', $routes);
    }
    
    /**
     * If logging has been enabled in the config file, then set up 
     * Zend's logging mechanism.
     * 
     * @uses Zend_Log
     * @return void
     **/
    public function initializeLogger()
    {
        $config = $this->getConfig('basic');
        
        if (!$config->log->errors && !$config->log->sql) {
            return;
        }
        
        $logFile = LOGS_DIR.DIRECTORY_SEPARATOR . 'errors.log';
        
        if (!is_writable($logFile)) {
            throw new Exception('Error log file cannot be written to. Please give this file read/write permissions for the web server.');
        }
        
        $writer = new Zend_Log_Writer_Stream($logFile);
        $logger = new Zend_Log($writer);
        
        $this->setLogger($logger);
    }
    
    /**
     * Initializes Omeka's ACL
     *
     * Checks to see if there is a serialized copy of the ACL in the database, 
     * then use that.  If not, then set up the ACL based on the hard-coded 
     * settings.
     * 
     * @uses Omeka_Acl
     * @todo ACL settings should be stored in the database.  When ACL settings
     * are properly stored in a normalized database configuration, then this
     * method should populate a new Acl instance with those settings and store
     * that Acl object in a session for quick access.
     * @return void
     **/
    public function initializeAcl()
    {
        $options = $this->getOptions();
        
        if ($serialized = $options['acl']) {
            $acl = unserialize($serialized);
        } else {
            $acl = $this->setupAcl();
        }
        
        $this->setAcl($acl);
    }
    
    /**
     * Load the ACL settings from a file and then save a serialized copy of 
     * the object to the database.
     * 
     * @return void
     **/
    public function setupAcl()
    {
        // Setup the ACL
        include CORE_DIR . DIRECTORY_SEPARATOR . 'acl.php';
        set_option('acl', serialize($acl));        
        return $acl;
    }
    
    /**
     * Initialize a copy of the plugin broker and load all the active plugins.
     *
     * This will also fire the 'initialize' hook for all plugins.  Note that
     * this hook fires before the front controller has been initialized or
     * dispatched, so the router is unavailable in this hook.
     * 
     * @uses Omeka_Plugin_Broker
     * @return void
     **/
    public function initializePluginBroker()
    {
        // Initialize the plugin broker with the database object and the 
        // plugins/ directory
        $broker = new Omeka_Plugin_Broker($this->getDb(), PLUGIN_DIR);
        $this->setPluginBroker($broker);
        
        $broker->loadActive();        
        
        // Fire all the 'initialize' hooks for the plugins
        $broker->initialize();
    }
    
    /**
     * Initialize the Auth object which handles authentication against the 
     * Omeka database.
     * 
     * @uses Zend_Auth
     * @return void
     **/
    public function initializeAuth()
    {
        $authPrefix = get_option('auth_prefix');
        
        // Set up the authentication mechanism with the specially generated 
        // prefix
        $auth = Zend_Auth::getInstance();
        
        require_once 'Zend/Auth/Storage/Session.php';
        $auth->setStorage(new Zend_Auth_Storage_Session($authPrefix));
        
        $this->setAuth($auth);
    }
    
    /**
     * Initialize the User object for the currently logged-in user.  If no user
     * has been authenticated, this value will be equivalent to false.
     * 
     * @uses Zend_Auth
     * @return void
     **/
    public function initializeCurrentUser()
    {
        $auth = $this->getAuth();
        
        $user = false;
        
        if ($auth->hasIdentity()) {
            $user_id = $auth->getIdentity();
            
            require_once 'User.php';
                        
            $user = $this->getDb()->getTable('User')->find($user_id);
        } 
        
        $this->setCurrentUser($user);
    }
    
    public function loadModelClasses()
    {
        require_once 'Item.php';
        require_once 'Option.php';
    }
    
    /**
     * @uses Zend_Controller_Front
     * @uses Zend_Controller_Router_Rewrite
     * @uses fire_plugin_broker()
     * @return void
     **/
    public function initializeFrontController()
    {
        // Front controller
        $front = Zend_Controller_Front::getInstance();
        $front->setControllerDirectory(CONTROLLER_DIR);
        $front->setRequest(new Zend_Controller_Request_Http);
        $front->setResponse(new Zend_Controller_Response_Http);
        // This plugin allows Omeka plugins to add controller directories to the 
        // 'default' namespace which prevents weird naming conventions like 
        // Contribution_IndexController.php and obviates the need to hack routes
        $front->registerPlugin(new Omeka_Controller_Plugin_PluginControllerHack);
        // This plugin allows for all functionality that is specific to the 
        // admin theme.
        $front->registerPlugin(new Omeka_Controller_Plugin_Admin);
        // This plugin allows for debugging request objects without inserting 
        // debugging code into the Zend Framework code files.        
        $front->registerPlugin(new Omeka_Controller_Plugin_Debug);
        
        // Routing
        $router = new Zend_Controller_Router_Rewrite();
        $router->addConfig($this->getConfig('routes'), 'routes');
        fire_plugin_hook('add_routes', $router);
        $front->setRouter($router);
        
        // Set contexts
        $this->setFrontController($front);
        $this->setRequest($front->getRequest());
        $this->setResponse($front->getResponse());
        
        // Action helpers
        $this->initializeActionHelpers();
    }
    
    private function initializeActionHelpers()
    {
        $this->initViewRenderer();
        $this->initResponseContexts();
        $this->initAclHelper();
    }
    
    private function initAclHelper()
    {
        $acl = $this->getAcl();
        $aclChecker = new Omeka_Controller_Action_Helper_Acl($acl);
        Zend_Controller_Action_HelperBroker::addHelper($aclChecker);
    }
    
    private function initViewRenderer()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = new Omeka_View();
        $viewRenderer->setView($view)
                     ->setViewSuffix('php');        
    }
    
    /**
     * Define the custom response format contexts for Omeka.
     * 
     * @link http://framework.zend.com/manual/en/zend.controller.actionhelpers.html#zend.controller.actionhelpers.contextswitch
     * 
     * Example of a definition of a response context through the ZF API:
     * 
     * $contexts->addContext('dc', array(
     *    'suffix'    => 'dc',
     *    'headers'   => array('Content-Type' => 'text/xml'),
     *    'callbacks' => array(
     *        'init' => 'atBeginningDoThis',
     *        'post' => 'afterwardsDoThis'
     *    ) 
     *  ));
     * 
     * @return void
     **/    
    private function initResponseContexts()
    {
        $contexts = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch');
        $contexts->setContextParam('output');
         $contexts->addContexts( array(
            'dc' => array(
                'suffix'    => 'dc',
                'headers'   => array('Content-Type' => 'text/xml')
            ),
            'rss2' => array(
                'suffix'    => 'rss2',
                'headers'   => array('Content-Type' => 'text/xml')
            )
        )); 
    }
    
    public function dispatch()
    {
        $front = $this->getFrontController();
        $front->dispatch();
    }   
    
    private function installerNotification()
    {
        include_once BASE_DIR . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'notify.php';
        exit;
    } 
}
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
 * @see Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

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
class Omeka_Core extends Zend_Controller_Plugin_Abstract
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
                               'loadModelClasses', 
                               'initializeOptions', 
                               'initializeSession',
                               'initializePluginBroker', 
                               'initializeAcl', 
                               'initializeAuth', 
                               'initializeCurrentUser', 
                               'initializeFrontController',
                               'initializeRoutes',
                               'initializeDebugging');
    
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
     * This makes Omeka_Core work as a Zend_Controller_Front plugin.
     * 
     **/
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        try {
            $this->initialize();
        } catch (Zend_Db_Adapter_Exception $e) {
            // Database adapter exceptions indicate that something has gone
            // wrong on the adapter level. Usually this will occur as a result of a
            // database not existing, or not being able to connect to the host.
            // This should redirect to an error page. For now, just dump the error.
            echo $e->getMessage();exit; 
        } catch (Zend_Db_Statement_Exception $e) {
            // Database statement exceptions indicate that something has gone
            // wrong within the actual database.  During initialization, this
            // will only occur when trying to access the 'options' table, so it
            // directly indicates that Omeka has not been installed. Since we're
            // going to continue dispatching in order to get to the install script,
            // load the skeleton of the initialization script.
            $this->setOmekaIsInstalled(false);
            $this->initializeFrontController();
        } catch (Zend_Config_Exception $e) {
            // These exceptions will be thrown for config files, when they don't
            // exist or are improperly structured. Should do something similar to
            // the database exception errors.
            echo "Error in Omeka's configuration file(s): " . $e->getMessage();exit; 
        } catch (Exception $e) {
            // No idea what this exception would be.  Just start crying.
            echo $e->getMessage();exit;
        }
    }
    
    /**
     * If Omeka has not been installed yet, make sure we dispatch to the
     *  notification in the InstallerController.
     * 
     **/
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if (!$this->omekaIsInstalled()) {
            $request->setControllerName('installer');
            $request->setActionName('notify');
        }        
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
        // Unfortunately we can't use the Zend_Loader instead, because it
        // throws warnings when it can't find a file. On the other hand,
        // Omeka::autoload() never tries to include the file if it doesn't
        // exist.
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
        require_once 'Omeka/Db.php';
        $db = $this->getConfig('db');
        
        // Fail on improperly configured db.ini file
        if (!isset($db->host) || ($db->host == 'XXXXXXX')) {
            throw new Zend_Config_Exception('Your Omeka database configuration file has not been set up properly.  Please edit the configuration and reload this page.');
        }
        
        $connectionParams = array('host'     => $db->host,
                                                'username' => $db->username,
                                                'password' => $db->password,
                                                'dbname'   => $db->name);
        
        // 'port' parameter was introduced in 0.10, conditional check needed
        // for backwards compatibility.
        if (isset($db->port)) {
            $connectionParams['port'] = $db->port;
        }
        
        $dbh = Zend_Db::factory('Mysqli', $connectionParams);
        
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
        $db = $this->getDb();
        
        // This will throw an exception if the options table does not exist
        $options = $db->fetchPairs("SELECT name, value FROM $db->Option");
        
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
        
        $db_file = BASE_DIR . DIRECTORY_SEPARATOR . 'db.ini';
        
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
     * @since 0.10 Plugins must use the 'define_acl' hook to modify ACL definitions.
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

        $acl = $this->setupAcl();
        
        $this->setAcl($acl);
        
        fire_plugin_hook('define_acl', $acl);
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
     * Initialize the session and customize the session name to prevent session
     * overlap between different applications that operate on the same server.
     * 
     * @return void
     **/
    public function initializeSession()
    {   
        // Look for the session name as the 'session.name' value in the 
        // config.ini file.  If it can't find that value (or it is blank), it
        // will automatically generate the session name based on the root URL
        // of this particular installation.
        $basicConfig = $this->getConfig('basic');
        $sessionName = (isset($basicConfig->session) && !empty($basicConfig->session->name)) 
                       ? $basicConfig->session->name
                       : preg_replace('/[^\w]+/', '', WEB_ROOT);
    
        Zend_Session::start(array(
            'name'=>$sessionName));
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
        $auth = Zend_Auth::getInstance();      
         
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
            $user = $auth->getIdentity();
            // This extra database call seems unnecessary at face value, but it
            // actually retrieves the entity metadata about the user as well as the
            // username/role info that is already stored in the auth identity.
            require_once 'User.php';
                        
            $user = $this->getDb()->getTable('User')->find($user->id);
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
        $front->addControllerDirectory(CONTROLLER_DIR, 'default');
            
        $this->_context->setFrontController($front);
                                                
        // Action helpers
        $this->initializeActionHelpers();        
        
        $front->registerPlugin(new Omeka_Controller_Plugin_ViewScripts);
    }
    
    /**
     * @since 0.10 'add_routes' hook is deprecated in favor of 'define_routes'.
     * @return void
     **/
    public function initializeRoutes()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->addConfig($this->getConfig('routes'), 'routes');
        
        fire_plugin_hook('define_routes', $router);
        
        // Deprecated in 0.10 (use 'define_routes' instead).
        fire_plugin_hook('add_routes', $router);
        $front->setRouter($router);
    }
    
    public function initializeDebugging()
    {
        $front = Zend_Controller_Front::getInstance();

        // Uncaught exceptions should bubble up to the browser level since we
        // are essentially in debug/install mode. Otherwise, we should make use
        // of the ErrorController, which WILL NOT LOAD IF YOU ENABLE EXCEPTIONS
        // (took me awhile to figure this out).
        if (($config = $this->getConfig('basic')) and ((boolean)$config->debug->exceptions)) {
            $front->throwExceptions(true);  
        }
        
        // This plugin allows for debugging request objects without inserting 
        // debugging code into the Zend Framework code files.        
        $front->registerPlugin(new Omeka_Controller_Plugin_Debug);
    }
    
    private function initializeActionHelpers()
    {
        $this->initViewRenderer();
        $this->initResponseContexts();
        $this->initAclHelper();
        $this->initSearchHelper();
    }
    
    private function initAclHelper()
    {
        // If the ACL has not been initialized, we should not enable this action helper.  
        // The ACL will not be initialized under the following conditions:
        // A) Installation.
        // B) Error conditions that occur before the ACL phase could be loaded.
        // C) Testing conditions which don't require use of the ACL.
        if ($acl = $this->getAcl()) {
            $aclChecker = new Omeka_Controller_Action_Helper_Acl($acl);
            Zend_Controller_Action_HelperBroker::addHelper($aclChecker);
        }
    }
    
    private function initSearchHelper()
    {
        $searchHelper = new Omeka_Controller_Action_Helper_SearchItems;
        Zend_Controller_Action_HelperBroker::addHelper($searchHelper);
    }
    
    private function initViewRenderer()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = new Omeka_View();
        $viewRenderer->setView($view)
                     ->setViewSuffix('php');  
                     
        // Register the view object so that it can be called by the view helpers.
        Zend_Registry::set('view', $view);   
    }
    
    /**
     * Define the custom response format contexts for Omeka.
     * 
     * Plugin writers should use the 'define_response_contexts' filter to modify
     * or expand the list of formats that existing controllers may respond to.
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
        Zend_Controller_Action_HelperBroker::addHelper(new Omeka_Controller_Action_Helper_ContextSwitch);
        $contexts = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch');
                
        $contexts->setContextParam('output');
                
        $contextArray = array(
             'dc' => array(
                 'suffix'    => 'dc',
                 'headers'   => array('Content-Type' => 'text/xml')
             ),
             'rss2' => array(
                 'suffix'    => 'rss2',
                 'headers'   => array('Content-Type' => 'text/xml')
             )
         );

        if ($pluginBroker = $this->getPluginBroker()) {
             $contextArray = $pluginBroker->applyFilters('define_response_contexts', $contextArray);
        }
        
        $contexts->addContexts($contextArray); 
    }       
}
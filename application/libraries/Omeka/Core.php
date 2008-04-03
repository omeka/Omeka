<?php 
require_once 'Omeka/Context.php';
/**
* 
*/
class Omeka_Core
{
    /**
     * 'Context' is a term that describes a pattern for storing site-wide data in a singleton
     * Stuff like the logger, acl, database objects, etc. is accessible through this object 
     *
     * @var string
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
    
    public function sanitizeMagicQuotes()
    {
        //Strip out those bastard slashes
        if(get_magic_quotes_gpc()) {
        	$_POST = stripslashes_deep($_POST);
        	$_REQUEST = stripslashes_deep($_REQUEST);
        	$_GET = stripslashes_deep($_GET);
        }      
    }
    
    public function initialize()
    {
        $this->sanitizeMagicQuotes();
        $this->initializeClassLoader();
        $this->initializeConfigFiles();
        $this->initializeDb();
        $this->initializeOptions();
        $this->loadModelClasses();
        $this->initializeAcl();
        $this->initializePluginBroker();
        $this->initializeAuth();
        $this->initializeFrontController();
        $this->initializeDefaultLogger();
    }
    
    /**
     * Provide phased loading of core Omeka functionality. Primarily used for Omeka scripts that run outside a web environment.
     * @param string $stopPhase The phase where the user wants loading to stop. 
     * @return void
     **/
    public function phasedLoading($stopPhase)
    {
        // Within this array put initializations in the most logical order.
        $phases = array(
            'sanitizeMagicQuotes', 
            'initializeClassLoader', 
            'initializeConfigFiles', 
            'initializeDb', 
            'initializeOptions', 
            'loadModelClasses', 
            'initializeAcl', 
            'initializePluginBroker', 
            'initializeAuth', 
            'initializeFrontController'
        );
        
        // Throw an error if the stop phase doesn't exist.
        if (!in_array($stopPhase, $phases)) {
            throw new Exception('The provided stop phase "'.$stopPhase.'" does not exist.');
        }
        
        // Load initialization callbacks in the proper order.
        foreach ($phases as $phase) {
            call_user_func(array($this, $phase));
            if ($phase == $stopPhase) break;
        }
    }
    
    public function initializeClassLoader()
    {
        require_once 'Omeka.php';
        spl_autoload_register(array('Omeka', 'autoload'));
    }
    
    /**
     * Initialize the default database connection for all Omeka requests
     *
     * @return void
     **/
    public function initializeDb()
    {
        try {
            $db = $this->getConfig('db');

        	//Fail on improperly configured db.ini file
        	if (!isset($db->host) or ($db->host == 'XXXXXXX')) {
        		throw new Zend_Config_Exception('Your Omeka database configuration file has not been set up properly.  Please edit the configuration and reload this page.');
        	}

        	$dsn = 'mysql:host='.$db->host.';dbname='.$db->name;
        	if(isset($db->port)) {
        		$dsn .= "port=" . $db->port;
        	}
	
        	$dbh = Zend_Db::factory('Mysqli', array(
        	    'host'     => $db->host,
        	    'username' => $db->username,
        	    'password' => $db->password,
        	    'dbname'   => $db->name));
        } 
        catch (Zend_Db_Adapter_Exception $e) {
            // perhaps a failed login credential, or perhaps the RDBMS is not running
        	echo $e->getMessage();exit;
        } 
        catch (Zend_Exception $e) {
            // perhaps factory() failed to load the specified Adapter class
        	echo $e->getMessage();exit;
        }
        catch (Zend_Config_Exception $e) {
        	echo $e->getMessage();exit;
        }
        catch (Exception $e) {
        	$this->installerNotification();
        }

        $db_obj = new Omeka_Db($dbh, $db->prefix);
                
        $this->setDb($db_obj);   
    }
    
    public function initializeOptions()
    {
        //Pull the options from the DB
        try {
            $db = $this->getDb();
        	$option_stmt = $db->query("SELECT * FROM $db->Option");
        	if(!$option_stmt) {
        		throw new Exception( 'Install me!' );
        	}
        	
        	$option_array = $option_stmt->fetchAll();

            // ****** CHECK TO SEE IF OMEKA IS INSTALLED ****** 
            if(!count($option_array)) {
            	throw new Exception( 'Install me!' );
            }
        } 
        catch (Exception $e) {
        	$this->installerNotification();
        }

        //Save the options so they can be accessed
        $options = array();
        foreach ($option_array as $opt) {
        	$options[$opt['name']] = $opt['value'];
        }
        
        $this->setOptions($options);
                
    }
    
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
    	Zend_Registry::set('db_ini', $db); 
 
        $this->setConfig('db', $db);
 
        $config = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'config.ini', 'site');
        
        Zend_Registry::set('config_ini', $config);
        
        $this->setConfig('basic', $config);
        
        $routes = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'routes.ini', null);
        Zend_Registry::set('routes_ini', $routes);
        
        $this->setConfig('routes', $routes);
    }
    
    public function initializeDefaultLogger()
    {
        $config = $this->getConfig('basic');
        if(isset($config->log)) {
            
        	$logger = new Omeka_Logger;

        	if(isset($config->log->sql) && $config->log->sql) {
        		$logger->setSqlLog(LOGS_DIR.DIRECTORY_SEPARATOR.'sql.log');
        		$logger->activateSqlLogging(true);	
        	}
        	if(isset($config->log->errors) && $config->log->errors) {
        		$logger->setErrorLog(LOGS_DIR.DIRECTORY_SEPARATOR.'errors.log');
        		$logger->activateErrorLogging(true);
        	}
        	$this->setLogger($logger);
        }        
    }
    
    public function initializeAcl()
    {
        //Setup the ACL
        include CORE_DIR . DIRECTORY_SEPARATOR .'acl.php';
                
        $this->setAcl($acl);     
    }
    
    public function initializePluginBroker()
    {
        //Activate the plugins
        require_once 'plugins.php';
        $plugin_broker = new PluginBroker;
    }
    
    public function initializeAuth()
    {
        $authPrefix = get_option('auth_prefix');

        //Set up the authentication mechanism with the specially generated prefix
        $auth = Zend_Auth::getInstance();

        require_once 'Zend/Auth/Storage/Session.php';
        $auth->setStorage(new Zend_Auth_Storage_Session($authPrefix));

        $this->setAuth($auth);
    }
    
    public function loadModelClasses()
    {
        require_once 'Item.php';
        require_once 'Option.php';
    }
    
    public function initializeFrontController()
    {
        // Initialize some stuff
        $front = Zend_Controller_Front::getInstance();
        $router = new Zend_Controller_Router_Rewrite();
        $router->addConfig(Zend_Registry::get('routes_ini'), 'routes');
        fire_plugin_hook('add_routes', $router);
        
        $router->setFrontController($front);
        $front->setRouter($router);

        $front->getDispatcher()->setFrontController($front);
        
        //This plugin redirects exceptions to the ErrorController (built in to ZF)
        $front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler());
        
        //This plugin redirects outdated installations to the UpgradeController
        $front->registerPlugin(new Omeka_Controller_Plugin_Upgrader());
        
        //This plugin allows Omeka plugins to add controller directories to the 'default' namespace
        //which prevents weird naming conventions like Contribution_IndexController.php
        //and obviates the need to hack routes
        $front->registerPlugin(new Omeka_Controller_Plugin_PluginControllerHack());
        
        //Disable the ViewRenderer until we can refactor Omeka codebase to use it
        $front->setParam('noViewRenderer', true);

        require_once 'Zend/Controller/Request/Http.php';
        $request = new Zend_Controller_Request_Http();

        // Removed 3/9/07 n8
        //Zend_Registry::set('request', $request);
        $front->setRequest($request);

        require_once 'Zend/Controller/Response/Http.php';
        $response = new Zend_Controller_Response_Http();
        // Removed 3/9/07 n8
        //Zend_Registry::set('response', $response);
        $front->setResponse($response);

        //$front->addControllerDirectory(array('default'=>CONTROLLER_DIR));
        $front->addControllerDirectory(CONTROLLER_DIR);   
        
        $this->setFrontController($front); 
        
        $this->setRequest($request);
        $this->setResponse($response);    
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
?>
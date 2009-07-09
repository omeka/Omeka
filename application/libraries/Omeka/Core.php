<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

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
class Omeka_Core extends Zend_Application
{
    /**
     * Array containing all core loading phase methods in sequential order. 
     * Modify this array if any phase is added or deleted.
     *
     * @access protected
     * @var array
     */
    protected $_phases = array('sanitizeMagicQuotes' => null, 
                               'initializeClassLoader' => null, 
                               'initializeConfigFiles' => 'Config', 
                               'initializeLogger' => 'Logger', 
                               'initializeDb' => 'Db', 
                               'initializeOptions' => 'Options', 
                               'initializePluginBroker' => 'PluginBroker', 
                               'initializeSession' => 'Session',
                               'initializePlugins' => 'Plugins',
                               'initializeAcl' => 'Acl', 
                               'initializeAuth' => 'Acl', 
                               'initializeCurrentUser' => 'CurrentUser', 
                               'initializeFrontController' => 'FrontController',
                               'initializeRoutes' => 'Router',
                               'initializeDebugging' => 'Debug');
    
    /**
     * Initialize the application.
     *
     * @return void
     **/
    public function __construct($environment = null, $options = null)
    {
        require_once 'globals.php';
        // For the sake of backwards compatibility with existing scripts that
        // instantiate Omeka_Core with no arguments.
        if (!$environment && !$options) {
            $environment = APPLICATION_ENV;
            $options = CONFIG_DIR . DIRECTORY_SEPARATOR . 'application.ini';
        }
        parent::__construct($environment, $options);
        
        $this->getBootstrap()->setContainer(Omeka_Context::getInstance());
    }
    
    /**
     * Delegate to the context object
     *
     * @return mixed
     **/
    public function __call($m, $a)
    {
        if (substr($m, 0, 10) == 'initialize') {
            $bootstrapResource = $this->_phases[$m];
            return $this->getBootstrap()->bootstrap($bootstrapResource);
        }
        
        return call_user_func_array(array($this->getBootstrap()->getContainer(), $m), $a);
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
        try {
            $this->sanitizeMagicQuotes();
            return $this->bootstrap();
        } catch (Zend_Db_Adapter_Exception $e) {
            // Database adapter exceptions indicate that something has gone
            // wrong on the adapter level. Usually this will occur as a result 
            // of a database not existing, or not being able to connect to the
            // host. This should redirect to an error page. For now, just dump
            // the error.
            echo $e->getMessage(); exit; 
        } catch (Zend_Db_Statement_Exception $e) {
            // Database statement exceptions indicate that something has gone
            // wrong within the actual database.  During initialization, this
            // will only occur when trying to access the 'options' table, so it
            // directly indicates that Omeka has not been installed. Since we're
            // going to continue dispatching in order to get to the install 
            // script, load the skeleton of the initialization script.
            $this->setOmekaIsInstalled(false);
            $bootstrap = $this->getBootstrap();
            if (!$bootstrap->hasResource('FrontController')) {
                $bootstrap->bootstrap('FrontController');
            }
            $frontController = $bootstrap->getResource('FrontController');
            $frontController->registerPlugin(new Omeka_Controller_Plugin_Installer());
            return $this;
        } catch (Zend_Config_Exception $e) {
            // These exceptions will be thrown for config files, when they don't
            // exist or are improperly structured. Should do something similar
            // to the database exception errors.
            echo "Error in Omeka's configuration file(s): " . $e->getMessage(); exit; 
        } catch (Exception $e) {
            // No idea what this exception would be.  Just start crying.
            echo $e->getMessage();exit;
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
        if (!array_key_exists($stopPhase, $this->_phases)) {
            exit("Error: The provided stop phase method \"$stopPhase\" does not exist.");
        }
        
        // Load initialization callbacks in the proper order.
        foreach ($this->_phases as $phase => $bootstrap) {
            $this->$phase();
            if ($phase == $stopPhase) {
                break;
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
        
    }
}
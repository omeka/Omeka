<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Core class used to bootstrap the Omeka environment.
 * 
 * Various duties include, but are not limited to setting up class autoload, 
 * database, configuration files, logging, plugins, front controller, etc.
 *
 * When any core resource returns from init(), the result is stored in the
 * bootstrap container. Other parts of the application can get the resources
 * from the bootstrap when needed.
 * 
 * @package Omeka\Application
 */
class Omeka_Application extends Zend_Application
{
    /**
     * Initialize the application.
     *
     * @param string $environment The environment name.
     * @param string|array|Zend_Config $options Application configuration.
     */
    public function __construct($environment, $options = null)
    {
        // Add functions to the global scope.
        require_once 'globals.php';
        
        // Set the configuration file if not passed.
        if (!$options) {
            $options = CONFIG_DIR . '/application.ini';
        }
        
        parent::__construct($environment, $options);
        Zend_Registry::set('bootstrap', $this->getBootstrap());
    }
    
    /**
     * Bootstrap the entire application.
     */
    public function initialize()
    {
        try {
            // Force the autoloader to be set up first.
            $this->getBootstrap()->bootstrap('Autoloader');
            return $this->bootstrap();
            
        } catch (Zend_Config_Exception $e) {
            $this->_displayErrorPage($e->getMessage(), 'Omeka Configuration Error');
        
        } catch (Zend_Db_Adapter_Mysqli_Exception $e) {
            $message = $e->getMessage() . ".\n\n";
            $message .= 'Confirm that the information in your db.ini file is correct.';
            $this->_displayErrorPage($message, 'Omeka Database Error'); 
        
        } catch (Omeka_Db_Migration_Exception $e) {
            $title = 'Cannot Upgrade: Need to Upgrade to Omeka 1.2.1';
            $message = 'You must upgrade to version 1.2.1 before continuing.'."\n\n";
            $message .= 'Please consult the <a href="http://omeka.org/codex/Upgrading">Upgrading</a> page on the Omeka codex, and <a href="http://omeka.org/files/omeka-1.2.1.zip">Downlodad Omeka 1.2.1</a>';
            $this->_displayErrorPage($message, $title);
        
        } catch (Exception $e) {
            $this->_displayErrorPage($e);
        }
        exit;
    }
    
    /**
     * Display the generic error page for all otherwise-uncaught exceptions.
     */
    public function run()
    {
        try {
            return parent::run();
        } catch (Exception $e) {
            $this->_displayErrorPage($e);
            exit;
        }
    }
    
    /**
     * Print an HTML page to display errors when starting the application.
     *
     * @param Exception $e
     * @param string $title The title of the error page.
     */
    private function _displayErrorPage($e, $title = null)
    {
        $logger = $this->getBootstrap()->getResource('Logger');
        if ($logger) {
            $logger->log($e, Zend_Log::ERR);
        } else {
            error_log("Omeka fatal error: $e");
        }
        $displayError = $this->getEnvironment() != 'production';
        header("HTTP/1.0 500 Internal Server Error");
        require VIEW_SCRIPTS_DIR . '/error/index.php';
    }
}

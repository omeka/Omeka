<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Core class used to bootstrap the Omeka environment.
 * 
 * Various duties include, but are not limited to, sanitizing magic_quotes,
 * setting up class autoload, database, configuration files, logging, plugins,
 * front controller, etc.
 *
 * When any core resource returns from init(), the result is stored in the
 * bootstrap container. Other parts of the application can get the resources
 * from the bootstrap when needed.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 */
class Omeka_Core extends Zend_Application
{
    /**
     * Initialize the application.
     *
     * @param string $environment Environment name.
     * @param string|array|Zend_Config $options Application configuration.
     */
    public function __construct($environment, $options = null)
    {
        require_once 'globals.php';
        
        // Set the configuration file if not passed.
        if (!$options) {
            $options = CONFIG_DIR . '/' . 'application.ini';
        }
        
        parent::__construct($environment, $options);
        
        Zend_Registry::set('bootstrap', $this->getBootstrap());
    }
    
    /**
     * Bootstrap the entire application.
     *
     * This will initialize all the elements of the application.
     * 
     * @return void
     */
    public function initialize()
    {
        try {
            // Force the autoloader to be set up first.
            $this->getBootstrap()->bootstrap('Autoloader');
            $this->sanitizeMagicQuotes();
            return $this->bootstrap();
        } catch (Zend_Config_Exception $e) {
            // These exceptions will be thrown for config files, when they don't
            // exist or are improperly structured. Should do something similar
            // to the database exception errors.
            $this->_displayErrorPage($e->getMessage(), 'Omeka Configuration Error');
        } catch (Zend_Db_Adapter_Mysqli_Exception $e) {
            $message = $e->getMessage() .'.'."\n\n";
            $message .= 'Confirm that the information in your db.ini file is correct.';
            $this->_displayErrorPage($message, 'Omeka Database Error'); 
        } catch (Omeka_Db_Migration_Exception $e) {
            $title = 'Cannot Upgrade: Need to Upgrade to Omeka 1.2.1';
            $message = 'You must upgrade to version 1.2.1 before continuing.'."\n\n";
            $message .= 'Please consult the <a href="http://omeka.org/codex/Upgrading">Upgrading</a> page on the Omeka codex, and <a href="http://omeka.org/files/omeka-1.2.1.zip">Downlodad Omeka 1.2.1</a>';
            $this->_displayErrorPage($message, $title);
        } catch (Exception $e) {
            // No idea what this exception would be.  Just start crying.
            $this->_displayErrorPage($e);
        }
        exit;
    }
    
    /**
     * If magic_quotes has been enabled, then strip all slashes from the $_GET, 
     * $_POST and $_REQUEST superglobals.
     * 
     * @return void
     */
    public function sanitizeMagicQuotes()
    {
        //Strip out those bastard slashes
        if (get_magic_quotes_gpc()) {
            $_POST = stripslashes_deep($_POST);
            $_REQUEST = stripslashes_deep($_REQUEST);
            $_GET = stripslashes_deep($_GET);
        }
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
     * @param string $message Error message to display.
     * @param Exception $e
     * @return void
     */
    private function _displayErrorPage($e, $title = null)
    {
        if (($logger = $this->getBootstrap()->getResource('Logger')))
        {
            $logger->log($e, Zend_Log::ERR);
        } else {
            error_log("Omeka fatal error: $e");
        }
        $displayError = $this->getEnvironment() != 'production';
        header("HTTP/1.0 500 Internal Server Error");
        require VIEW_SCRIPTS_DIR . '/error/index.php';
    }
}

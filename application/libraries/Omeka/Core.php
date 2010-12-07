<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
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
 * This class delegates to the Omeka_Context instance, which holds all state
 * that get initialized by this class.  Methods can be called on this class
 * as though it were an instance of Omeka_Context.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @uses Omeka_Context
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Omeka_Core extends Zend_Application
{
    /**
     * Array containing all core loading phase methods in sequential order. 
     * Modify this array if any phase is added or deleted.
     *
     * @var array
     */
    protected $_phases = array('sanitizeMagicQuotes' => null, 
                               'initializeConfigFiles' => 'Config', 
                               'initializeLogger' => 'Logger', 
                               'initializeDb' => 'Db', 
                               'initializeOptions' => 'Options',
                               'initializePluginBroker' => 'PluginBroker', 
                               'initializeSession' => 'Session',
                               'initializePlugins' => 'Plugins',
                               'initializeAcl' => 'Acl', 
                               'initializeAuth' => 'Auth', 
                               'initializeCurrentUser' => 'CurrentUser', 
                               'initializeFrontController' => 'FrontController',
                               'initializeRoutes' => 'Router',
                               'initializeDebugging' => 'Debug');
    
    /**
     * Initialize the application.
     *
     * @param string $environment Environment name.
     * @param string|array|Zend_Config $options Application configuration.
     */
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
     * Delegate to the context object.
     *
     * @param string $m Method called.
     * @param array $a Arguments to method.
     * @return mixed
     */
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
     */
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
     */
    public function initialize()
    {
        try {
            $this->sanitizeMagicQuotes();
            return $this->bootstrap();
        } catch (Zend_Config_Exception $e) {
            // These exceptions will be thrown for config files, when they don't
            // exist or are improperly structured. Should do something similar
            // to the database exception errors.
            $message = "Error in Omeka's configuration file(s): " . $e->getMessage();
        } catch (Exception $e) {
            // No idea what this exception would be.  Just start crying.
            $message = $e->getMessage();
        }
        $this->_displayErrorPage($message, $e);
        exit;
    }
    
    /**
     * Provide phased loading of core Omeka functionality. Primarily used for 
     * Omeka scripts that run outside a web environment.
     *
     * @param string $stopPhase The phase where the user wants loading to stop. 
     * @return void
     */
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
     * Display the generic error page for all otherwise-uncaught exceptions.
     */
    public function run()
    {
        try {
            return parent::run();
        } catch (Exception $e) {
            $this->_displayErrorPage($e->getMessage(), $e);
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
    private function _displayErrorPage($message = '', Exception $e)
    {
        header("HTTP/1.0 500 Internal Server Error");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Error</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body {font:62.5% "Lucida Grande",Helvetica, Arial, sans-serif; background:#eae9db;color: #333;min-width:882px;}
h1 {font-weight:normal; font-size:2.2em; margin-bottom:1em; line-height:1em;margin-right:12em;}
h1 {color:#a74c29;}
h1 {font-family:Georgia, Times, "Times New Roman", serif;}
#primary {width: 666px; padding: 18px; background: #fff; margin: 36px auto;}
#primary { background:#fff; padding:18px;border:1px solid #d7d5c4; border-width: 3px 0;}
p {font-size:1.2em; line-height:1.5em; margin-bottom:1.5em;}
</style>
</head>
<body>
<div id="wrap">
    <div id="primary">
        <h1>Omeka Has Encountered an Error</h1>
        <?php if (ini_get('display_errors')): ?>
            <p><?php echo $message; ?></p>
            <pre><?php echo $e->getTraceAsString(); ?></pre>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php
    }
}

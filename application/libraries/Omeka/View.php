<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @see Zend_View_Abstract
 */
require_once 'Zend/View/Abstract.php';

/**
 * Customized subclass of Zend Framework's View class.
 *
 * This adds the correct script paths for themes and plugins
 * so that controllers can render the appropriate scripts.
 *
 * This will also inject directly into the view scripts
 * all variables that have been assigned to the view,
 * so that theme writers can access them as $item instead of
 * $this->item, for example.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_View extends Zend_View_Abstract
{    
    /**
     * Maintains a key => value pairing corresponding to hard path => web path for possible assets for Omeka views
     *
     * @var array
     **/
    protected $_asset_paths = array();
    
    public function __construct($config = array())
    {
        parent::__construct($config);         
        $this->initPaths();     
        
        // Setting the XHTML1_STRICT doctype fixes validation errors for ZF's form elements
        $this->doctype()->setDoctype('XHTML1_STRICT');
        
        $this->_loadCustomThemeScripts();
    }
    
    /**
     * Load order for view scripts:
     * themes
     * plugins
     * application/views
     *
     * Load order for asset paths:
     * themes
     * plugins
     * shared
     * application/views
     *
     * @todo Is there any reason why shared paths shouldn't load view scripts?
     * 
     **/
    private function initPaths()
    {
        //Baseline view scripts get checked first
        $this->addScriptPath(VIEW_SCRIPTS_DIR);
        
        //View scripts and shared directory get checked for assets 
        $this->addAssetPath(VIEW_SCRIPTS_DIR, WEB_VIEW_SCRIPTS);
        
        //Next add script paths for plugins and themes (in that order)
        //The admin bootstrap defines this simple constant to let us know
        if (defined('ADMIN')) {
            $this->addPluginPaths('admin');
            $this->addThemePaths('admin');
        } else {
            $this->addPluginPaths('public');
            $this->addThemePaths('public');
        }
        
        $this->addHelperPath(HELPER_DIR, 'Omeka_View_Helper');        
    }
    
    public function addPluginPaths($themeType)
    {
        if ($broker = Omeka_Context::getInstance()->getPluginBroker()) {
            $broker->loadThemeDirs($this, $themeType);
        }
    }
    
    /**
     * Retrieve the option from the database that contains the directory of
     * the theme to render. 
     * 
     * @param string $type Currently either 'admin' or 'public'
     * @return string
     **/
    protected function getThemeOption($type)
    {
        $options = Omeka_Context::getInstance()->getOptions();
        return @$options[$type . '_theme'];
    }
    
    /**
     * Add asset and script paths for the chosen theme
     * 
     * @param string Currently 'admin' or 'public'
     * @return void
     **/
    protected function addThemePaths($themeType)
    {                    
        if ($themeName = $this->getThemeOption($themeType)) {
            $scriptPath = THEME_DIR.DIRECTORY_SEPARATOR . $themeName;
            $this->addScriptPath($scriptPath);
            $this->addAssetPath($scriptPath, WEB_THEME.DIRECTORY_SEPARATOR . $themeName);            
        }    
    }
    
    /**
     * @return Zend_Controller_Request_Http
     **/
    public function getRequest()
    {
        return Omeka_Context::getInstance()->getRequest();
    }
    
    /**
     * @return Zend_Controller_Request_Http
     **/    
    public function getResponse()
    {
        return Omeka_Context::getInstance()->getResponse();
    }
    
    /**
     * @return array
     **/
    public function getAssetPaths()
    {
        return $this->_asset_paths;
    }

    public function addAssetPath($physical, $web)
    {
        array_unshift($this->_asset_paths, array($physical, $web));
    }
    
    public function setAssetPath($physical, $web)
    {
        $this->_asset_paths = array();
        $this->_asset_paths[] = array($physical, $web);
    }
    
    /**
     * Look for a 'custom.php' script in all script paths and run the file if it exists.
     * 
     * @return void
     **/
    private function _loadCustomThemeScripts()
    {
        foreach ($this->getScriptPaths() as $path) {
            $customScriptPath = $path . 'custom.php';
            if (file_exists($customScriptPath)) {
                include_once $customScriptPath;
            }
        }
    }
        
    /**
     * This allows for variables set to the view object
     * to be referenced in the view script by their actual name.
     * 
     * For example, in a controller you might do something like:
     * $view->assign('themes', $themes);
     * Normally in the view you would then reference $themes through:
     * $this->themes;
     * 
     * 
     * Now you can reference it simply by using:
     * $themes;
     * 
     * 
     */
    public function _run() {
        $vars = $this->getVars();
                
        require_once HELPERS;
        
        try {
            extract($vars);    
            include func_get_arg(0);
        } catch (Exception $e) {
            
            // Exceptions should not be uncaught at this stage of execution. 
            // This is b/c the only PHP executed beyond this point are theme 
            // functions.
            echo 'Error:' . $e->getMessage();
            
            if ($config = Omeka_Context::getInstance()->getConfig('basic')) {
                //Display a lot of info if exceptions are turned on
                if ($config->debug->exceptions) {    
                    echo nl2br( $e->getTraceAsString() );
                }                
            }
        }
    }
}
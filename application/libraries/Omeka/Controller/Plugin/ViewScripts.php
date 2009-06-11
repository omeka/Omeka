<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Controller_Plugin_ViewScripts extends Zend_Controller_Plugin_Abstract
{
    protected $_view;
    
    protected $_dbOptions = array();
    
    public function __construct($options)
    {
        $this->_dbOptions = $options;
    }
    
    /**
     * This handles adding the appropriate view scripts directories for a given
     * request.  This is pretty much the glue between the plugin broker and the
     * View object, since it uses data from the plugin broker to determine what
     * script paths will be available to the view.  
     * 
     * @param string
     * @return void
     **/
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        
        // Getting the module name from the request object is pretty much the main
        // reason why this needs to be in a controller plugin and can't be localized
        // to the view script.
        
        $moduleName = $request->getModuleName();
        $isPluginModule = !in_array($moduleName, array('default', null));
        $themeType = is_admin_theme() ? 'admin' : 'public';
        
        
        if ($isPluginModule) {
            // Make it so that plugin view/assets load before the theme (and only for the specific plugin/theme).
            $this->_setupPathsForPlugin($moduleName, $themeType);
        } else {
            // Make it so that plugin view/assets load after the theme (and for all possibly plugins).
            $this->_setupPathsForTheme($themeType);
        }
        
        $this->_loadCustomThemeScripts();
        
        // var_dump($this->_view);exit;
    }


    /**
     *  If you're in a plugin, check in this order:
     *    * plugin view scripts (only for that plugin <-- fixes a bug)
     *    * plugin shared dir
     *    * theme view scripts
     *    * theme shared dir
     * 
     * This means that it needs to add the paths in the reverse order of what needs
     * to be checked first, so theme paths first and then plugin paths.
     * 
     */
    protected function _setupPathsForPlugin($moduleName, $themeType)
    {
        $this->_addThemePaths($themeType);        
        $this->_addPathsForModule($themeType, $moduleName);
    }

    /**
     *  If you're in one of the themes, check in this order:
     *    * theme view scripts
     *    * theme shared dir
     *    * all plugin view scripts
     *    * all plugin shared dirs
     * 
     */
    protected function _setupPathsForTheme($themeType)
    {
        $this->_addPathsForModule($themeType);        
        $this->_addThemePaths($themeType);
    }
    
    protected function _addPathsForModule($themeType, $moduleName = null)
    {
        // This part of the controller plugin depends on Omeka's plugin broker.
        // If the plugin broker is not installed, can't do anything.
        $pluginBroker = Omeka_Context::getInstance()->getPluginBroker();
        if (!$pluginBroker) {
            return;
        }
        
        $scriptDirs = $pluginBroker->getModuleViewScriptDirs($moduleName);
        
        // IF we have chosen a specific module to add paths for.
        if ($moduleName and $scriptDirs[$themeType]) {
            foreach ($scriptDirs[$themeType] as $scriptPath) {
                $this->_addPathToView($scriptPath);
            }
        } else {
            // We have not chosen a specific module to add paths for, so just add
            // them all (for the specific theme type, 'admin' or 'public').
            foreach ($scriptDirs as $moduleName => $scriptPathSet) {
                if ($scriptPathSet[$themeType]) {
                    foreach ($scriptPathSet[$themeType] as $scriptPath) {
                        $this->_addPathToView($scriptPath);
                    }
                }
            }
        }
    }
    
    protected function _addPathToView($scriptPath)
    {
        $view = $this->_getView();
        $physicalPath = PLUGIN_DIR . DIRECTORY_SEPARATOR . $scriptPath;
        $webPath      = WEB_PLUGIN . '/' . $scriptPath;
        $view->addAssetPath($physicalPath, $webPath);
        $view->addScriptPath($physicalPath);
    }
    
    protected function _getView()
    {
        if (!$this->_view) {
            $this->_view = Zend_Registry::get('view');
        }
        
        return $this->_view;
    }
    
    protected function _addSharedViewsDir()
    {
        $view = $this->_getView();
        
        //Baseline view scripts get checked first
        $view->addScriptPath(VIEW_SCRIPTS_DIR);
        
        //View scripts and shared directory get checked for assets 
        $view->addAssetPath(VIEW_SCRIPTS_DIR, WEB_VIEW_SCRIPTS);
    }
        
    /**
     * Look for a 'custom.php' script in all script paths and run the file if it exists.
     * 
     * @return void
     **/
    protected function _loadCustomThemeScripts()
    {
        $view = $this->_getView();
        foreach ($view->getScriptPaths() as $path) {
            $customScriptPath = $path . 'custom.php';
            if (file_exists($customScriptPath)) {
                include_once $customScriptPath;
            }
        }
    }

    
    /**
     * Theme can be either 'public' or 'admin'.
     * 
     * @param string
     * @return void
     **/
    protected function _addThemePaths($theme)
    {
        $this->_addSharedViewsDir();
        
        $view = $this->_getView();
        if ($themeName = $this->getThemeOption($theme)) {
            $scriptPath = THEME_DIR.DIRECTORY_SEPARATOR . $themeName;
            $view->addScriptPath($scriptPath);
            $view->addAssetPath($scriptPath, WEB_THEME.'/' . $themeName);            
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
        return @$this->_dbOptions[$type . '_theme'];
    }
}

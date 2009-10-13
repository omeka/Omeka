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
    
    protected $_baseThemePath;
    protected $_webBaseThemePath;
    
    public function __construct($options)
    {
        $this->_dbOptions = $options['dbOptions'];
        $this->_baseThemePath = $options['baseThemePath'];
        $this->_webBaseThemePath = $options['webBaseThemePath'];
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
    }

    /**
     * Sets up the asset paths for the plugin.
     *  
     * If you're in a plugin, check in this order:
     *    1. plugin view scripts (only for that plugin)
     *    2. plugin view scripts for other plugins
     *    3. theme view scripts
     * 
     * This means that it needs to add the paths in the reverse order of what needs
     * to be checked first, so theme paths first and then plugin paths.
     * 
     * @param string $pluginModuleName The module name for the plugin
     * @param string $themeType The type of theme: 'admin' or 'public'
     * @return void
     */
    protected function _setupPathsForPlugin($pluginModuleName, $themeType)
    {
        $this->_addThemePaths($themeType);        
        $this->_addPluginPaths($themeType, $pluginModuleName);
    }

    /**
     *  Sets up the asset paths for the theme
     * 
     *  If you're in one of the themes, check in this order:
     *    1. theme view scripts
     *    2. all plugin view scripts
     * 
     * @param string $themeType The type of theme: 'admin' or 'public'
     * @return void
     */
    protected function _setupPathsForTheme($themeType)
    {
        $this->_addPluginPaths($themeType);        
        $this->_addThemePaths($themeType);
    }
    
    /**
     *  Adds asset paths for a plugin.
     * 
     * @param string $pluginModuleName The module name for the plugin
     * @param string $themeType The type of theme: 'admin' or 'public'
     * @return void
     */
    protected function _addPluginPaths($themeType, $pluginModuleName = null)
    {
        // This part of the controller plugin depends on Omeka's plugin broker.
        // If the plugin broker is not installed, can't do anything.
        $pluginBroker = Omeka_Context::getInstance()->getPluginBroker();
        if (!$pluginBroker) {
            return;
        }
                
        // If we have chosen a specific module to add paths for.
        if ($pluginModuleName) {
            
            // We need to add the scripts in reverse order if how they will be found.
             
            // add the scripts from the other modules
            $otherPluginScriptDirs = $pluginBroker->getModuleViewScriptDirs(null);
            foreach ($otherPluginScriptDirs as $otherPluginModuleName => $scriptPathSet) {
                if ($otherPluginModuleName != $pluginModuleName && $scriptPathSet[$themeType]) {
                    foreach ($scriptPathSet[$themeType] as $scriptPath) {
                        $this->_addPathToView($scriptPath);
                    }
                }
            }
            
            // add the scripts from the first module
            $pluginScriptDirs = $pluginBroker->getModuleViewScriptDirs($pluginModuleName);
            if ($pluginScriptDirs[$themeType]) {
                foreach ($pluginScriptDirs[$themeType] as $scriptPath) {
                    $this->_addPathToView($scriptPath);
                }
            }
            
        } else {
            // We have not chosen a specific module to add paths for, so just add
            // them all (for the specific theme type, 'admin' or 'public').
            $pluginScriptDirs = $pluginBroker->getModuleViewScriptDirs(null);
            foreach ($pluginScriptDirs as $moduleName => $scriptPathSet) {
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
            $scriptPath = $this->_baseThemePath . DIRECTORY_SEPARATOR . $themeName;
            $view->addScriptPath($scriptPath);
            $view->addAssetPath($scriptPath, $this->_webBaseThemePath . '/' . $themeName);            
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

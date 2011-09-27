<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Sets up view script search paths on a per-request basis.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Plugin_ViewScripts extends Zend_Controller_Plugin_Abstract
{
    /**
     * Registered view object.
     *
     * @var Zend_View
     */
    protected $_view;
    
    /**
     * List of options from the database.
     *
     * @var array
     */
    protected $_dbOptions = array();
    
    /**
     * Base path to themes directory.
     *
     * @var string
     */
    protected $_baseThemePath;
    
    /**
     * Base web-accesible path to themes.
     *
     * @var string
     */
    protected $_webBaseThemePath;
    
    /**
     * MVC plugin behaviors class.
     * 
     * @var Omeka_Plugin_Mvc
     */
    protected $_pluginMvc;
    
    /**
     * @param array $options List of options.
     * @param Omeka_Plugin_Mvc $pluginMvc Plugin MVC class.
     */
    public function __construct($options, Omeka_Plugin_Mvc $pluginMvc)
    {
        $this->_dbOptions = $options['dbOptions'];
        $this->_baseThemePath = $options['baseThemePath'];
        $this->_webBaseThemePath = $options['webBaseThemePath'];
        $this->_pluginMvc = $pluginMvc;
    }

    /**
     * Add the appropriate view scripts directories for a given request.
     * This is pretty much the glue between the plugin broker and the
     * View object, since it uses data from the plugin broker to determine what
     * script paths will be available to the view.  
     * 
     * @param Zend_Controller_Request_Abstract $request Request object.
     * @return void
     */
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
     * Set up the asset paths for a plugin.
     *  
     * If you're in a plugin, check in this order:
     *    1. plugin view scripts (only for that plugin)
     *    2. plugin view scripts for other plugins
     *    3. theme view scripts
     * 
     * This means that it needs to add the paths in the reverse order of what needs
     * to be checked first, so theme paths first and then plugin paths.
     * 
     * @param string $pluginModuleName The module name for the plugin.
     * @param string $themeType The type of theme: 'admin' or 'public'.
     * @return void
     */
    protected function _setupPathsForPlugin($pluginModuleName, $themeType)
    {
        $this->_addThemePaths($themeType);        
        $this->_addPluginPaths($themeType, $pluginModuleName);
    }

    /**
     * Set up the asset paths for the theme.
     * 
     * If you're in one of the themes, check in this order:
     *    1. theme view scripts
     *    2. all plugin view scripts
     * 
     * @param string $themeType The type of theme: 'admin' or 'public'.
     * @return void
     */
    protected function _setupPathsForTheme($themeType)
    {
        $this->_addPluginPaths($themeType);        
        $this->_addThemePaths($themeType);
    }
    
    /**
     * Add asset paths for a plugin.
     * 
     * @param string $pluginModuleName The module name for the plugin.
     * @param string $themeType The type of theme: 'admin' or 'public'.
     * @return void
     */
    protected function _addPluginPaths($themeType, $pluginModuleName = null)
    {                
        // If we have chosen a specific module to add paths for.
        if ($pluginModuleName) {
            
            // We need to add the scripts in reverse order if how they will be found.
             
            // add the scripts from the other modules
            $otherPluginScriptDirs = $this->_pluginMvc->getModuleViewScriptDirs(null);
            foreach ($otherPluginScriptDirs as $otherPluginModuleName => $scriptPathSet) {
                if ($otherPluginModuleName != $pluginModuleName && isset($scriptPathSet[$themeType])) {
                    foreach ($scriptPathSet[$themeType] as $scriptPath) {
                        $this->_addPathToView($scriptPath);
                    }
                }
            }
            
            // add the scripts from the first module
            $pluginScriptDirs = $this->_pluginMvc->getModuleViewScriptDirs($pluginModuleName);
            if ($pluginScriptDirs[$themeType]) {
                foreach ($pluginScriptDirs[$themeType] as $scriptPath) {
                    $this->_addPathToView($scriptPath);
                }
            }

            // Adds plugin-specific scripts for themes (these take precedence over everything)
            $this->_addOverridePathForPlugin($themeType, $pluginModuleName);
            
        } else {
            // We have not chosen a specific module to add paths for, so just add
            // them all (for the specific theme type, 'admin' or 'public').
            $pluginScriptDirs = $this->_pluginMvc->getModuleViewScriptDirs(null);
            foreach ($pluginScriptDirs as $moduleName => $scriptPathSet) {
                if (array_key_exists($themeType, $scriptPathSet)) {
                    foreach ($scriptPathSet[$themeType] as $scriptPath) {
                        $this->_addPathToView($scriptPath);
                    }
                }
            }
        }
    }
    
    /**
     * Add a new script path for a plugin to the view.
     *
     * @param string $scriptPath Path from plugins dir to script dir.
     * @return void
     */
    protected function _addPathToView($scriptPath)
    {
        $view = $this->_getView();
        $physicalPath = PLUGIN_DIR . '/' . $scriptPath;
        $webPath      = WEB_PLUGIN . '/' . $scriptPath;
        $view->addAssetPath($physicalPath, $webPath);
        $view->addScriptPath($physicalPath);
    }
    
    /**
     * Gets the view from the registry.
     *
     * The initial call to the registry caches the view in this class.
     *
     * @return Zend_View
     */
    protected function _getView()
    {
        if (!$this->_view) {
            $this->_view = Zend_Registry::get('view');
        }
        
        return $this->_view;
    }
    
    /**
     * Add the global views from the view scripts directory to the view.
     *
     * @return void
     */
    protected function _addSharedViewsDir()
    {
        $view = $this->_getView();
        
        //Baseline view scripts get checked first
        $view->addScriptPath(VIEW_SCRIPTS_DIR);
        
        //View scripts and shared directory get checked for assets 
        $view->addAssetPath(VIEW_SCRIPTS_DIR, WEB_VIEW_SCRIPTS);
    }
    
    /**
     * Add script and asset paths for a theme to the view.
     * 
     * @param string $theme Theme type; either 'public' or 'admin'.
     * @return void
     */
    protected function _addThemePaths($theme)
    {
        $this->_addSharedViewsDir();
        
        $view = $this->_getView();
        if ($themeName = $this->getThemeOption($theme)) {
            $scriptPath = $this->_baseThemePath . '/' . $themeName;
            $view->addScriptPath($scriptPath);
            $view->addAssetPath($scriptPath, $this->_webBaseThemePath . '/' . $themeName);            
        }
    }

    /**
     * Add theme view path for override views for a given plugin.
     *
     * @param string $theme Theme type; 'public' or 'admin'
     * @param string $pluginModuleName
     */
    protected function _addOverridePathForPlugin($theme, $pluginModuleName)
    {
        if (($themeName = $this->getThemeOption($theme))) {
            $view = $this->_getView();
            $scriptPath = $this->_baseThemePath . '/' . $themeName . '/' . $pluginModuleName;
            $view->addScriptPath($scriptPath);
            $view->addAssetPath($scriptPath, $this->_webBaseThemePath . '/' . $themeName . '/' . $pluginModuleName);
        }
    }
    
    /**
     * Retrieve the option from the database that contains the directory of
     * the theme to render. 
     * 
     * @param string $type Currently either 'admin' or 'public'.
     * @return string
     */
    protected function getThemeOption($type)
    {
        return Theme::getCurrentThemeName($type);
    }
}

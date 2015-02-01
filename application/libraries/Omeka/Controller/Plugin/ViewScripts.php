<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Sets up view script search paths on a per-request basis.
 * 
 * @package Omeka\Controller\Plugin
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

        $pluginScriptDirs = $this->_pluginMvc->getViewScriptDirs($themeType);

        // Remove the current plugin, if any, from the set of "normal" plugin paths
        if ($isPluginModule && isset($pluginScriptDirs[$moduleName])) {
            $currentPluginScriptDirs = $pluginScriptDirs[$moduleName];
            unset($pluginScriptDirs[$moduleName]);
        }

        // Add all the "normal" plugin paths
        foreach ($pluginScriptDirs as $modulePaths) {
            $this->_addPathsToView($modulePaths);
        }

        // Add the theme and core paths
        $this->_addThemePaths($themeType);

        // Add plugin and theme-override paths for current plugin
        if ($isPluginModule) {
            if (isset($currentPluginScriptDirs)) {
                $this->_addPathsToView($currentPluginScriptDirs);
            }
            $this->_addOverridePathForPlugin($themeType, $moduleName);
        }
    }

    /**
     * Add multiple script paths.
     * 
     * @param array $paths The paths to add.
     */
    protected function _addPathsToView($paths)
    {
        foreach ($paths as $path) {
            $this->_addPathToView($path);
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

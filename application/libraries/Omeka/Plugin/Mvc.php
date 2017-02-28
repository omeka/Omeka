<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Connects plugins with Omeka's model-view-controller system.
 * 
 * @package Omeka\Plugin
 */
class Omeka_Plugin_Mvc
{
    /**
     * Path to the root plugins directory.
     * @var string
     */
    protected $_basePath;

    /** 
     * View script directories that have been added by plugins.
     * @var array
     */
    protected $_pluginViewDirs = array(
        'admin' => array(),
        'public' => array(),
    );

    /**
     * View helper directories from plugins.
     * @var array
     */
    protected $_pluginHelpersDirs = array();

    /**
     * @param string $basePath Plugins directory path.
     */
    public function __construct($basePath)
    {
        $this->_basePath = $basePath;
    }

    /**
     * Add a theme directory to the list of plugin-added view directories.
     *
     * Used by the add_theme_pages() helper to create a list of directories that
     * can store static pages that integrate into the themes.
     *
     * @param string $pluginDirName Plugin name.
     * @param string $path Path to directory to add.
     * @param string $themeType Type of theme ('public', 'admin', or 'shared').
     * @param string $moduleName MVC module name.
     */
    protected function addThemeDir($pluginDirName, $path, $themeType, $moduleName)
    {
        //Path must begin from within the plugin's directory
        $path = $pluginDirName . '/' . $path;

        switch ($themeType) {
            case 'public':
                $this->_pluginViewDirs['public'][$moduleName][] = $path;
                break;
            case 'admin':
                $this->_pluginViewDirs['admin'][$moduleName][] = $path;
                break;
            case 'shared':
                $this->_pluginViewDirs['public'][$moduleName][] = $path;
                $this->_pluginViewDirs['admin'][$moduleName][] = $path;
                break;
            default:
                break;
        }
    }

    /**
     * Retrieve the list of plugin-added view script directories.
     *
     * @param string $themeType Type of theme (public or admin)
     * @return array Module-name-indexed directory names.
     */
    public function getViewScriptDirs($themeType)
    {
        return $this->_pluginViewDirs[$themeType];
    }

    /**
     * Get all the existing plugin view helper dirs, indexed by plugin name.
     *
     * @return array
     */
    public function getHelpersDirs()
    {
        return $this->_pluginHelpersDirs;
    }

    /**
     * Make an entire directory of controllers available to the front
     * controller.
     * 
     * This has to use addControllerDirectory() instead of addModuleDirectory()
     * because module names are case-sensitive and module directories need to be
     * lowercased to conform to Zend's weird naming conventions.
     *
     * @param string $pluginDirName Plugin name.
     * @param string $moduleName MVC module name.
     */
    public function addControllerDir($pluginDirName, $moduleName)
    {
        $contrDir = PLUGIN_DIR . '/' . $pluginDirName . '/' . 'controllers';
        Zend_Controller_Front::getInstance()->addControllerDirectory($contrDir, $moduleName);
    }

    /**
     * Set up the following directory structure for plugins:
     * 
     *      controllers/
     *      models/
     *      libraries/
     *      views/
     *          admin/
     *          public/
     *          shared/
     * 
     *  This also adds these folders to the correct include paths.
     *  
     * @param string $pluginDirName Plugin name.
     */
    public function addApplicationDirs($pluginDirName)
    {
        $baseDir = $this->_basePath . '/' . $pluginDirName;

        $modelDir = $baseDir . '/models';
        $controllerDir = $baseDir . '/controllers';
        $librariesDir = $baseDir . '/libraries';
        $viewsDir = $baseDir . '/views';
        $adminDir = $viewsDir . '/admin';
        $publicDir = $viewsDir . '/public';
        $sharedDir = $viewsDir . '/shared';
        $helpersDir = $viewsDir . '/helpers';

        //Add 'models' and 'libraries' directories to the include path
        if (is_dir($modelDir) && !$this->_hasIncludePath($modelDir)) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $modelDir);
        }

        if (is_dir($librariesDir) && !$this->_hasIncludePath($librariesDir)) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $librariesDir);
        }

        if (is_dir($helpersDir)) {
            $this->_pluginHelpersDirs[$pluginDirName] = $helpersDir;
        }

        $moduleName = $this->_getModuleName($pluginDirName);

        //If the controller directory exists, add that
        if (is_dir($controllerDir)) {
            $this->addControllerDir($pluginDirName, $moduleName);
        }

        if (is_dir($sharedDir)) {
            $this->addThemeDir($pluginDirName, 'views/shared', 'shared', $moduleName);
        }

        if (is_dir($adminDir)) {
            $this->addThemeDir($pluginDirName, 'views/admin', 'admin', $moduleName);
        }

        if (is_dir($publicDir)) {
            $this->addThemeDir($pluginDirName, 'views/public', 'public', $moduleName);
        }
    }

    /**
     * Retrieve the module name for the plugin (based on the directory name
     * of the plugin).
     * 
     * @param string $pluginDirName Plugin name.
     * @return string Plugin MVC module name.
     */
    protected function _getModuleName($pluginDirName)
    {
        // Module name needs to be lowercased (plugin directories are not,
        // typically).  Module name needs to go from camelCased to dashed
        // (ElementSets --> element-sets).
        $inflector = new Zend_Filter_Word_CamelCaseToDash();
        $moduleName = strtolower($inflector->filter($pluginDirName));
        return $moduleName;
    }

    /**
     * Check include path to see if it already contains a specific path.
     * 
     * @param string $path
     * @return bool
     */
    private function _hasIncludePath($path)
    {
        $paths = explode(PATH_SEPARATOR, get_include_path());
        return in_array($path, $paths, true);
    }
}

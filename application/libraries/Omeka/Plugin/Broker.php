<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @author CHNM
 **/

/**
 * Plugin Broker for Omeka.
 *
 * @todo Plugin broker should have one responsibility: dealing with plugin hooks 
 * (and filters) that are called during program execution.  It should not interact
 * with the database, it should not load the plugins from the filesystem.
 * 
 * This handles installation, loading and calling hooks for plugins.  The
 * syntax for activating plugin hooks on the Broker is as simple as calling
 * it as a method on the broker.
 *
 * For example, $broker->add_action_contexts($controller) would call the 
 * 'add_action_contexts' on all plugins, and it would provide the controller
 * object as the first argument to all implementations of that hook. 
 **/
class Omeka_Plugin_Broker 
{    
    protected $_basePath = array();
    
    /**
     * Array of hooks that have been implemented for plugins.
     *
     * @var array
     **/
    protected $_callbacks = array();
        
    /**
     * The directory name of the current plugin (used for calling hooks)
     *
     * @var string
     **/
    protected $_current;
                
    // View script directories that have been added by plugins
    protected $_pluginViewDirs = array();
            
    public function __construct($pathToPlugins) 
    {        
        // Should be able to delegate to the plugin filters
        $this->_delegates = array();
        $this->_delegates[] = new Omeka_Plugin_Filters($this);
        
        $this->_basePath = $pathToPlugins;
    }
        
    /**
     * Check if the plugin is active, then enable the hook for it
     *
     * @param string $hook
     * @param string $callback
     * @return void
     **/
    public function addHook($hook, $callback)
    {    
        $currentPluginDirName = $this->getCurrentPluginDirName();          
        $this->_callbacks[$hook][$currentPluginDirName] = $callback;
    }
    
    /**
     * Gets the hook for a plugin
     *
     * @param string $pluginDirName
     * @param string $callback
     * @return void
     **/
    public function getHook($pluginDirName, $hook)
    {        
        if (is_array($this->_callbacks[$hook])) {
            return $this->_callbacks[$hook][$pluginDirName];
        }
    }
    
    /**
     * The plugin helper functions do not have any way of determining what
     *  plugin to is currently in focus.  These get/setCurrentPluginDirName methods
     *  allow the broker to know how to delegate to specific plugins if necessary.
     * 
     * @param string $pluginDirName
     * @return void
     **/
    public function setCurrentPluginDirName($pluginDirName)
    {
        $this->_current = $pluginDirName;
    }
    
    public function getCurrentPluginDirName()
    {
        return $this->_current;
    }
    
    /**
     * Configures the plugin
     *
     * @param string $pluginDirName
     * @return void
     **/
    public function config($pluginDirName)
    {
        //Check if the POST is empty, then check for a configuration form    
        if (empty($_POST)) {

            $config_form_hook = $this->getHook($pluginDirName, 'config_form');
    
            //If there is a configuration form available, load that and return the output for rendering later
            if ($config_form_hook) {
                
                require_once HELPERS;
                
                ob_start();
                call_user_func_array($config_form_hook, array($_POST)); 
                $config = ob_get_clean();    
                
                return $config;
            }
        
        //Data has been POSTed to the configuration mechanism
        } else {
            
            //Run the 'config' hook, then run the rest of the installer
            $config_hook = $this->getHook($pluginDirName, 'config');
            
            if ($config_hook) {
                call_user_func_array($config_hook, array($_POST));
            }
        }
    }
        
    /**
     * used by the add_theme_pages() helper to create a list of directories that can store static pages that integrate into the themes
     *
     * @param string $pluginDirName
     * @param string $path
     * @param string $themeType
     * @param string $moduleName
     * @return void
     **/
    protected function addThemeDir($pluginDirName, $path, $themeType, $moduleName)
    {
        if (!in_array($themeType, array('public','admin','shared'))) {
            return false;
        }
        
        //Path must begin from within the plugin's directory
        
        $path = $pluginDirName . DIRECTORY_SEPARATOR . $path;
                
        switch ($themeType) {
            case 'public':
                $this->_pluginViewDirs[$moduleName]['public'][] = $path;
                break;
            case 'admin':
                $this->_pluginViewDirs[$moduleName]['admin'][] = $path;
                break;
            case 'shared':
                $this->_pluginViewDirs[$moduleName]['public'][] = $path;
                $this->_pluginViewDirs[$moduleName]['admin'][] = $path;
                break;
            default:
                break;
        }
    }
    
    public function getModuleViewScriptDirs($moduleName=null)
    {
        if ($moduleName) {
            return $this->_pluginViewDirs[$moduleName];
        }
        return $this->_pluginViewDirs;
    }
    
    /**
     * This will make an entire directory of controllers available to the front controller.
     * 
     * This has to use addControllerDirectory() instead of addModuleDirectory() because module names
     * are case-sensitive and module directories need to be lowercased to conform to Zend's weird naming conventions.
     *
     * @param string $pluginDirName
     * @param string $moduleName 
     * @return void
     **/
    public function addControllerDir($pluginDirName, $moduleName)
    {                
        $contrDir = PLUGIN_DIR . DIRECTORY_SEPARATOR . $pluginDirName . DIRECTORY_SEPARATOR . 'controllers';
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
     * @param string $pluginDirName
     * @return void
     **/
    public function addApplicationDirs($pluginDirName)
    {        
        $baseDir = $this->_basePath . DIRECTORY_SEPARATOR . $pluginDirName;
        
        $modelDir      = $baseDir . DIRECTORY_SEPARATOR  . 'models';
        $controllerDir = $baseDir . DIRECTORY_SEPARATOR  . 'controllers';
        $librariesDir  = $baseDir . DIRECTORY_SEPARATOR  . 'libraries';
        $viewsDir      = $baseDir . DIRECTORY_SEPARATOR  . 'views';
        $adminDir      = $viewsDir . DIRECTORY_SEPARATOR . 'admin';
        $publicDir     = $viewsDir . DIRECTORY_SEPARATOR . 'public';
        $sharedDir     = $viewsDir . DIRECTORY_SEPARATOR . 'shared';
        
        //Add 'models' and 'libraries' directories to the include path
        if (file_exists($modelDir)) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $modelDir );
        }
        
        if (file_exists($librariesDir)) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $librariesDir);
        }
        
        $moduleName = $this->_getModuleName($pluginDirName);

        //If the controller directory exists, add that 
        if (file_exists($controllerDir)) {
            $this->addControllerDir($pluginDirName, $moduleName);   
        }
        
        if (file_exists($sharedDir)) {
            $this->addThemeDir($pluginDirName, 'views' . DIRECTORY_SEPARATOR . 'shared', 'shared', $moduleName);
        }
        
        if (file_exists($adminDir)) {
            $this->addThemeDir($pluginDirName, 'views' . DIRECTORY_SEPARATOR . 'admin', 'admin', $moduleName);
        }

        if (file_exists($publicDir)) {
            $this->addThemeDir($pluginDirName, 'views' . DIRECTORY_SEPARATOR . 'public', 'public', $moduleName);
        }

    }
    
    /**
     * Retrieve the module name for the plugin (based on the directory name
     * of the plugin).
     * 
     * @param string $pluginDirName
     * @return string
     **/
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
     * @see Omeka_Plugin_Broker::__call()
     * @param string Name of the hook.
     * @param array Arguments that are passed to each hook implementation.
     * @return array Keyed to the names of plugins, this will contain an array of 
     * all the return values of the hook implementations.
     **/
    public function callHook($hook, $args)
    {
        if (empty($this->_callbacks[$hook])) {
            return;
        }
        
        $return_values = array();        
        foreach ($this->_callbacks[$hook] as $pluginDirName => $callback) {
            if ($this->isActive($pluginDirName)) {
                //Make sure the callback executes within the scope of the current plugin
                $this->setCurrentPluginDirName($pluginDirName);
                $return_values[$pluginDirName] = call_user_func_array($callback, $args);
            }
        }
        
        // Reset the value for current plugin after this loop finishes
        $this->setCurrentPluginDirName(null);
        
        return $return_values;        
    }
    
    /**
     * This handles dispatching all plugin hooks.
     *
     * Check for delegating to other classes that handle plugin API stuff first,
     * i.e. Omeka_Plugin_Filters etc.
     *
     * @see Omeka_Plugin_Broker::__construct()
     * @return mixed
     **/
    public function __call($hook, $args) 
    {
        // Delegation
        foreach ($this->_delegates as $delegator) {
            if(method_exists($delegator, $hook)) {
                return call_user_func_array(array($delegator, $hook), $args);
            }
        }
        
        // Call the hook for the plugins
        return $this->callHook($hook, $args);
    }
}
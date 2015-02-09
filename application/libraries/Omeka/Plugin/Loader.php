<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Loads plugins for any given request.  
 * 
 * This will iterate through the plugins root directory and load all plugin.php 
 * files by require()'ing them.
 * 
 * @package Omeka\Plugin\Loader
 */
class Omeka_Plugin_Loader
{
    /**
     * Plugin broker object.
     *
     * @var Omeka_Plugin_Broker
     */
    protected $_broker;
    
    /**
     * Plugin INI reader object.
     *
     * @var Omeka_Plugin_Ini
     */
    protected $_iniReader;
    
    /**
     * Plugin MVC object.
     *
     * @var Omeka_Plugin_Mvc
     */
    protected $_mvc;
    
    /**
     * Plugins directory.
     *
     * @var string
     */
    protected $_basePath;
    
    /**
     * An array of all plugins (installed or not) that are currently located
     * in the plugins/ directory.
     *
     * @var array List of Plugin objects.
     */
    protected $_plugins = array();
    
    /**
     * @param Omeka_Plugin_Broker $broker Plugin broker.
     * @param Omeka_Plugin_Ini $iniReader plugin.ini reader.
     * @param Omeka_Plugin_Mvc $mvc Plugin MVC object.
     * @param string $pluginsBaseDir Plugins directory.
     */
    public function __construct(Omeka_Plugin_Broker $broker, 
                                Omeka_Plugin_Ini $iniReader,
                                Omeka_Plugin_Mvc $mvc,
                                $pluginsBaseDir)
    {
        $this->_broker = $broker;
        $this->_iniReader = $iniReader;
        $this->_mvc = $mvc;
        $this->_basePath = $pluginsBaseDir;
    }
    
    /**
     * Load a list of plugins.
     * 
     * @param array $plugins List of Plugin records to load.  
     * @param boolean $force If true, throws exceptions for plugins that cannot
     * be loaded for some reason.
     * @throws Omeka_Plugin_Loader_Exception
     * @return void
     */
    public function loadPlugins(array $plugins, $force = false)
    {
        // Register the entire list of plugins prior to loading them.  The advantage
        // to this is that all plugin dependencies will (hopefully) be available
        // to the loader.
        foreach ($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }

        foreach ($plugins as $plugin) {
            $this->load($plugin, $force);
        }
    }
    
    /**
     * Register a plugin so that it can be accessed by other plugins (if necessary)
     * during the load process.  
     * 
     * There should only be a single instance of a plugin per directory name.  
     * Registering a plugin more than once, i.e. loading a plugin again after the
     * first time failed, will not cause a problem as long as the same instance
     * was registered.
     *
     * @param Plugin $plugin Record of plugin to register.
     * @throws Omeka_Plugin_Loader_Exception
     * @return void
     */
    public function registerPlugin(Plugin $plugin)
    {
        $dirName = $plugin->getDirectoryName();
        if (array_key_exists($dirName, $this->_plugins) && $this->_plugins[$dirName] !== $plugin) {
            throw new Omeka_Plugin_Loader_Exception(sprintf("Plugin named '%s' has already been loaded/registered.", $dirName));
        }
        $this->_plugins[$dirName] = $plugin;
    }
    
    /**
     * Return whether a plugin is registered or not.
     * 
     * @param Plugin $plugin
     * @return boolean Whether the plugin is registered or not.
     */
    public function isRegistered(Plugin $plugin)
    {
        $dirName = $plugin->getDirectoryName();
        return array_key_exists($dirName, $this->_plugins) && $this->_plugins[$dirName] === $plugin;
    }
                
    /**
     * Load a plugin (and make sure the plugin API is available).
     * 
     * To be loaded, the plugin must be installed, active, and not have a newer 
     * version. If loaded, the plugin will attempt to first load all plugins, 
     * both required and optional, that the plugin uses.  However, it will not 
     * load a plugin that it uses if that plugin is not installed and activated.
     * 
     * @param Plugin $plugin
     * @param boolean $force If true, throws exceptions if a plugin can't be 
     * loaded.
     * @param array $pluginsWaitingToLoad Plugins waiting to be loaded
     * @throws Omeka_Plugin_Loader_Exception
     * @return void
     */
    public function load(Plugin $plugin, $force = false, $pluginsWaitingToLoad = array())
    {           
        $this->registerPlugin($plugin);
        
        $this->_iniReader->load($plugin);
        
        $pluginDirName = $plugin->getDirectoryName();
        if (!$this->_canLoad($plugin, $force)) {
            if ($force) {
                throw new Omeka_Plugin_Loader_Exception(sprintf("The %s plugin could not be loaded.", $pluginDirName));
            } else {
                return;
            }
        }

        // If the current plugin is already on the waiting list, don't load it.
        if (array_key_exists(spl_object_hash($plugin), $pluginsWaitingToLoad)) {
            return;
        } else {
            // Otherwise add the current plugin to the waiting list.
            $pluginsWaitingToLoad[spl_object_hash($plugin)] = $plugin;
        }
                
        // Load the required plugins
        $requiredPluginDirNames = $plugin->getRequiredPlugins();
        foreach($requiredPluginDirNames as $requiredPluginDirName) {
            if (!($requiredPlugin = $this->getPlugin($requiredPluginDirName))) {
                // If we can't find one of the required plugins, loading should
                // fail.
                if ($force) {
                    throw new Omeka_Plugin_Loader_Exception(sprintf("The required plugin '%s' could not be found.", $requiredPluginDirName));
                } else {
                    return;
                }
            }
            
            // If the required plugin is already loaded, do not attempt to load
            // it a second time.
            if ($requiredPlugin->isLoaded()) {
                continue;
            }
            $this->load($requiredPlugin, $force, $pluginsWaitingToLoad);
            
            // make sure the required plugin is loaded.
            // if a required plugin of the plugin cannot be loaded, 
            // then do not load the plugin
            if (!$requiredPlugin->isLoaded()) {
                return;
            }
        }
        
        // Load the optional plugins
        $optionalPluginDirNames = $plugin->getOptionalPlugins();
        foreach($optionalPluginDirNames as $optionalPluginDirName) {

            if (!($optionalPlugin = $this->getPlugin($optionalPluginDirName))) {
                // If we can't find one of the optional plugins, it should skip it and try to load the next one.                
                continue;
            }
                        
            // If the optional plugin is already loaded, do not attempt to load
            // it a second time.
            if ($optionalPlugin->isLoaded()) {
                continue;
            }            
            
            // If the optional plugin cannot load, then fail silently
            $this->load($optionalPlugin, false, $pluginsWaitingToLoad);
        }

        // add the plugin dir paths and require the plugin files
        $this->_mvc->addApplicationDirs($pluginDirName);

        $this->_loadPluginBootstrap($plugin);
        
        // This won't work unless the plugin has already been loaded.
        $plugin->setHasConfig((bool) $this->_broker->getHook($plugin->getDirectoryName(), 'config'));
        
        // remember that the plugin is loaded
        $plugin->setLoaded(true);
    }
    
    /**
     * Determine whether or not a plugin can be loaded.  To be loaded, it must
     * meet the following criteria:
     *  - Has a plugin.php file.
     *  - Is installed.
     *  - Is active.
     *  - Meets the minimum required version of Omeka (in plugin.ini).
     *  - Is not already loaded.
     *  - Does not have a new version available.
     *
     * @param Plugin $plugin Plugin to test.
     * @param boolean $force If true, throw an exception if the plugin can't
     * be loaded.
     * @return boolean
     */
    protected function _canLoad($plugin, $force)
    {
        $pluginDirName = $plugin->getDirectoryName();

        $error = false;

        if (!$this->hasPluginBootstrap($plugin)) {
            $error = "'%s' has no valid bootstrap file.";
        } else if (!$plugin->isInstalled()) {
            $error = "'%s' has not been installed.";
        } else if (!$plugin->isActive()) {
            $error = "'%s' has not been activated.";
        } else if (!$plugin->meetsOmekaMinimumVersion()) {
            $error = "'%s' requires a newer version of Omeka.";
        } else if ($plugin->hasNewVersion()) {
            $error = "'%s' must be upgraded to the new version before it can be loaded.";
        } else if ($plugin->isLoaded()) {
            $error = "'%s' cannot be loaded twice.";
        }

        if ($error && $force) {
            throw new Omeka_Plugin_Loader_Exception(
                sprintf($error, $plugin->getDirectoryName())
            );
        } else {
            return !$error;
        }
    }

    /**
     * Check whether a plugin has a bootstrap file.
     * 
     * @param string|Plugin $pluginDirName
     * @return boolean
     */
    public function hasPluginBootstrap($pluginDirName)
    {
        if ($pluginDirName instanceof Plugin) {
            $pluginDirName = $pluginDirName->getDirectoryName();
        }
        
        $pluginClassFilePath = $this->getPluginClassFilePath($pluginDirName);
        
        // Check if the plugin.php file exists.
        if (file_exists($this->getPluginFilePath($pluginDirName))) {
            return true;
        // Check if the valid plugin class exists.
        } else if (file_exists($pluginClassFilePath)) {
            require_once $pluginClassFilePath;
            if (is_subclass_of($this->getPluginClassName($pluginDirName), 'Omeka_Plugin_AbstractPlugin')) {
                return true;
            } else {
                return false;
            }
        // The plugin has no bootstrap.
        } else {
            return false;
        }
    }
    
    /**
     * Return the valid plugin class name.
     * 
     * @param string $pluginDirName
     * @return string
     */
    public function getPluginClassName($pluginDirName)
    {
        return "{$pluginDirName}Plugin";
    }
    
    /**
     * Return the path to the plugin.php file.
     * 
     * @param string $pluginDirName
     * @return string
     */
    public function getPluginFilePath($pluginDirName)
    {
        return "{$this->_basePath}/$pluginDirName/plugin.php";
    }
    
    /**
     * Return the path to the plugin class file.
     * 
     * @param string $pluginDirName
     * @param string $pluginClassName
     * @return string
     */
    public function getPluginClassFilePath($pluginDirName)
    {
        return "{$this->_basePath}/$pluginDirName/{$this->getPluginClassName($pluginDirName)}.php";
    }
    
    /**
     * Return a list of all the plugins that have been loaded (or attempted to
     * be loaded) thus far.
     *
     * @return array List of Plugin objects.
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }
    
    /**
     * Get a plugin object by name (plugin subdirectory name).
     *
     * @param string $directoryName Plugin name.
     * @return Plugin|null 
     */
    public function getPlugin($directoryName)
    {
        if (array_key_exists($directoryName, $this->_plugins)) {
            return $this->_plugins[(string)$directoryName];
        }
        return null;
    }
    
    /**
     * Loads the plugin bootstrap file for a plugin.
     *
     * @param Plugin $plugin
     * @return void
     */
    protected function _loadPluginBootstrap(Plugin $plugin)
    {
        $pluginDirName = $plugin->getDirectoryName();
        $this->_broker->setCurrentPluginDirName($pluginDirName);
        
        // Bootstrap plugin.php if it exists.
        $pluginFilePath = $this->getPluginFilePath($pluginDirName);
        if (file_exists($pluginFilePath)) {
            require $pluginFilePath;
        // Otherwise bootstrap the plugin class.
        } else {
            require_once $this->getPluginClassFilePath($pluginDirName);
            $pluginClassName = $this->getPluginClassName($pluginDirName);
            $pluginClass = new $pluginClassName;
            $pluginClass->setUp();
        }
        
        // Reset the current plugin.
        $this->_broker->setCurrentPluginDirName(null);
    }
}

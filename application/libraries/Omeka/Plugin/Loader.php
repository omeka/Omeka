<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Loads plugins for any given request.  
 * 
 * This will iterate through the plugins root directory and load all plugin.php 
 * files by require()'ing them.
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
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
    
    private $_requireOnce = true;
    
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
     * Flag to determine whether or not plugin.php should be require'd or 
     * require_once'd. This flag is true by default.
     *
     * @param boolean $flag
     */
    public function setRequireOnce($flag)
    {
        $this->_requireOnce = $flag;
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
     * @return void
     */
    public function registerPlugin(Plugin $plugin)
    {
        $dirName = $plugin->getDirectoryName();
        if (array_key_exists($dirName, $this->_plugins) && $this->_plugins[$dirName] !== $plugin) {
            throw new Omeka_Plugin_Loader_Exception("Plugin named '$dirName' has already been loaded/registered.");
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
     * @return void
     */
    public function load(Plugin $plugin, $force = false, $pluginsWaitingToLoad = array())
    {           
        $this->registerPlugin($plugin);
        
        $this->_iniReader->load($plugin);
        if ($plugin->getRequireOnce() === null) {
            $plugin->setRequireOnce($this->_requireOnce);
        }
        
        $pluginDirName = $plugin->getDirectoryName();
        if (!$this->_canLoad($plugin, $force)) {
            if ($force) {
                throw new Omeka_Plugin_Loader_Exception("The $pluginDirName plugin could not be loaded.");
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
                    throw new Omeka_Plugin_Loader_Exception("The required plugin '$requiredPluginDirName' could not be found.");
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
        $loadCriteria = array(
            array(
                'check' => !$this->hasPluginBootstrap($plugin),
                'exception' => "'$pluginDirName' plugin directory does not contain a 'plugin.php' file."),
            array(
                'check' => !$plugin->isInstalled(),
                'exception' => "'$pluginDirName' has not been installed."),
            array(
                'check' => !$plugin->isActive(),
                'exception' => "'$pluginDirName' has not been activated."),
            array(
                'check' => !$plugin->meetsOmekaMinimumVersion(),
                'exception' => "'$pluginDirName' requires a newer version of Omeka."),
            // Cannot upgrade a plugin if we do this check when trying to force
            // the plugin to load.
            array(
                'check' => $plugin->hasNewVersion(),
                'exception' => "'$pluginDirName' must be upgraded to the new version before it can be loaded."),
            array(
                'check' => $plugin->isLoaded(),
                'exception' => "'$pluginDirName' cannot be loaded twice.")
        );
        
        foreach ($loadCriteria as $criteria) {
            if ($criteria['check']) {
                if ($force) {
                    throw new Omeka_Plugin_Loader_Exception($criteria['exception']);
                } else {
                    return false;
                }
            }
        }
        
        return true;
    }
                        
    /**
     * Return whether a plugin has a plugin.php file.
     * 
     * @param string|Plugin $pluginDirName Plugin object or directory name.
     * @return boolean
     */
    public function hasPluginBootstrap($pluginDirName)
    {
        if ($pluginDirName instanceof Plugin) {
            $pluginDirName = $pluginDirName->getDirectoryName();
        }
        
        return file_exists($this->getPluginFilePath($pluginDirName));
    }
        
    /**
     * Returns the path to the plugin.php file
     * 
     * @param string $pluginDirName Plugin directory name.
     * @return string
     */
    public function getPluginFilePath($pluginDirName)
    {
        return $this->_basePath . '/' . $pluginDirName . '/' . 'plugin.php';
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
     * Loads the plugin bootstrap (plugin.php) file for a plugin.
     *
     * @param Plugin $plugin Plugin to bootstrap.
     * @return void
     */
    protected function _loadPluginBootstrap(Plugin $plugin)
    {
        $pluginDirName = $plugin->getDirectoryName();
        
        // set the current plugin
        $this->_broker->setCurrentPluginDirName($pluginDirName);
        $path = $this->getPluginFilePath($pluginDirName);
        if ($plugin->getRequireOnce()) {
            require_once $path;
        } else {
            require $path;
        }

        // set the current plugin back to null
        $this->_broker->setCurrentPluginDirName(null);
    }
}

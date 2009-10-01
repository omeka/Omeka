<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Loads plugins for any given request.  
 * 
 * This will iterate through the plugins root directory and load all plugin.php 
 * files by require'ing them.
 * 
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Plugin_Loader
{
    protected $_broker;
    protected $_iniReader;
    protected $_mvc;
    protected $_basePath;
    
    /**
     * An array of all plugins (installed or not) that are currently located
     * in the plugins/ directory
     *
     * @var array List of Plugin objects.
     **/
    protected $_plugins = array();
                
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
     */
    public function loadPlugins(array $plugins, $force = false)
    {
        // Index the entire list of plugins prior to loading them.  The advantage
        // to this is that all plugin dependencies will (hopefully) be available
        // to the loader.
        $this->_indexPlugins($plugins);

        foreach ($plugins as $plugin) {
            $this->_iniReader->load($plugin);                     
            $this->load($plugin, $force);
        }
    }
    
    /**
     * Index a set of plugins for easy access by the load() method.
     */
    protected function _indexPlugins(array $plugins)
    {
        foreach ($plugins as $plugin) {
            $dirName = $plugin->getDirectoryName();
            if (array_key_exists($dirName, $this->_plugins)) {
                throw new Omeka_Plugin_Loader_Exception("Plugin named '$dirName' has already been loaded/indexed.");
            }
            $this->_plugins[$dirName] = $plugin;
        }
    }
                
    /**
     * Loads a plugin (and make sure the plugin API is available)
     * 
     * To be loaded, the plugin must be installed, active, and does not have a newer version.
     * If loaded, the plugin will attempt to first load all plugins, both required and optional, that the plugin uses.  
     * However, it will not load a plugin that it uses, if that plugin is not installed and activated
     * 
     * @param Plugin $plugin
     * @param boolean $force If true, throws exceptions if a plugin can't be 
     * loaded.
     * @param array $pluginsWaitingToLoad Plugins waiting to be loaded
     * @return void
     **/
    public function load(Plugin $plugin, $force = false, $pluginsWaitingToLoad = array())
    {           
        $pluginDirName = $plugin->getDirectoryName();

        if (!$this->_canLoad($plugin, $force)) {
            return;
        }

        // If the current plugin is already on the waiting list, don't load it.
        if (array_key_exists(spl_object_hash($plugin), $pluginsWaitingToLoad)) {
            return;
        } else {
            // Otherwise add the current plugin to the waiting list.
            $pluginsWaitingToLoad[spl_object_hash($plugin)] = $plugin;
        }
        
        // Load all of a plugin's dependencies.
        $requiredPluginDirNames = $plugin->getRequiredPlugins();

        foreach($requiredPluginDirNames as $requiredPluginDirName) {
            if (!($requiredPlugin = $this->getPlugin($requiredPluginDirName))) {
                // If we can't find one of the required plugins, loading should
                // fail.
                if ($force) {
                    throw new Omeka_Plugin_Loader_Exception("'$requiredPluginDirName' required plugin could not be found.");
                } else {
                    return;
                }
            }
            $this->load($requiredPlugin, $force, $pluginsWaitingToLoad);
            
            // make sure the required plugin is loaded.
            // if a required plugin of the plugin cannot be loaded, 
            // then do not load the plugin
            if (!$requiredPlugin->isLoaded()) {
                return;
            }
        }

        // load the optional plugins for the plugin
        $optionalPluginDirNames = $plugin->getOptionalPlugins();
        foreach($optionalPluginDirNames as $optionalPluginDirName) {
            $optionalPlugin = $this->getPlugin($optionalPluginDirName);
            if ($optionalPlugin) {
                // Should an optional plugin ever be forced to load?  Debugging
                // situations may require this, but most will not.  Will this 
                // fail during installation ?
                $this->load($optionalPlugin, $force, $pluginsWaitingToLoad);
            }
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
     *  - Meets the minimum required version of Omeka (in plugin.ini)
     *  - Is not already loaded (Why?)
     *  - Does not have a new version available (Why?)
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
     * Returns whether a plugin has a plugin.php file
     * 
     * @param string|Plugin $pluginDirName
     * @return boolean
     **/
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
     * @param string $pluginDirName
     * @return string
     **/
    public function getPluginFilePath($pluginDirName)
    {
        return $this->_basePath . DIRECTORY_SEPARATOR . $pluginDirName . DIRECTORY_SEPARATOR . 'plugin.php';
    }
        
    /**
     * Return a list of all the plugins that have been loaded (or attempted to
     * be loaded) thus far.
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }
    
    /**
     * @param string $directoryName
     * @return Plugin|null 
     */
    public function getPlugin($directoryName)
    {
        return $this->_plugins[(string)$directoryName];
    }
    
    protected function _loadPluginBootstrap(Plugin $plugin)
    {
        $pluginDirName = $plugin->getDirectoryName();
        
        // set the current plugin
        $this->_broker->setCurrentPluginDirName($pluginDirName);
        require_once $this->getPluginFilePath($pluginDirName);

        // set the current plugin back to null
        $this->_broker->setCurrentPluginDirName(null);
    }
}

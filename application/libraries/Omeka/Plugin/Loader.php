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
     * Load a list of plugins from the database.  Only loads the plugins that 
     * are both installed and active.  The others are indexed accordingly.
     * 
     * @todo Refactor the code that gets new plugins into a separate class, one
     * that is called only in the PluginsController (init() method).
     * @param array
     * @param boolean $force If true, throws exceptions for plugins that cannot
     * be loaded for some reason.
     */
    public function loadPlugins(array $plugins, $force = false)
    {
        // @todo The full list of plugins should also include all of the 
        // plugins that are required
        $this->_plugins = $this->_indexPlugins(array_merge($this->getNewPlugins($plugins), $plugins));
                
        // First off, we're not trying to run through and load every damn plugin.
        // Just the ones that were passed via this method call.
        foreach ($plugins as $plugin) {
            $this->_iniReader->load($plugin);
            
            $plugin->setInstalled(true);
                        
            $this->load($plugin, $force);
        }
    }
    
    /**
     * @todo This should really be in a class by itself (along with the PluginTable
     * data retrieval), for setting up the list of plugins to be loaded into Omeka.
     */
    public function getNewPlugins(array $existingPlugins)
    {
        $dirListing = $this->_getDirectoryList();
        $existingPluginNames = array();
        foreach ($existingPlugins as $plugin) {
            $existingPluginNames[] = $plugin->getDirectoryName();
        }
        $newPluginDirNames = array_diff($dirListing, $existingPluginNames);
        
        $newPlugins = array();
        foreach ($newPluginDirNames as $pluginDirName) {
            $newPlugin = new Plugin;
            $newPlugin->setDirectoryName($pluginDirName);
            $newPlugins[] = $newPlugin;
        }
        return $newPlugins;
    }
    
    /**
     * Index a set of plugins for easy access by the load() method.
     */
    protected function _indexPlugins(array $plugins)
    {
        $indexedPlugins = array();
        foreach ($plugins as $plugin) {
            $indexedPlugins[$plugin->getDirectoryName()] = $plugin;
        }
        return $indexedPlugins;
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
     * @param array $pluginDirNamesWaitingToBeLoaded The array of the directory names of dependent plugins waiting to be loaded
     * @return void
     **/
    public function load(Plugin $plugin, $force = false, $pluginDirNamesWaitingToBeLoaded = array())
    {           
        $pluginDirName = $plugin->getDirectoryName();

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
        $loadCriteria = array(
            array(
                'check' => !$this->hasPluginFile($pluginDirName),
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
                    throw new Exception($criteria['exception']);
                } else {
                    return;
                }
            }
        }
        
        if (in_array($pluginDirName, $pluginDirNamesWaitingToBeLoaded)) {
            return;
        }
                                     
        // add the current plugin to directory names waiting to be loaded
        $pluginDirNamesWaitingToBeLoaded[] = $pluginDirName;
        
        // load the required plugins for the plugin
        $requiredPluginDirNames = $plugin->getRequiredPlugins();

        foreach($requiredPluginDirNames as $requiredPluginDirName) {
            $requiredPlugin = $this->getPlugin($requiredPluginDirName);
            $this->load($requiredPlugin, $force, $pluginDirNamesWaitingToBeLoaded);
            
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
            $this->load($optionalPlugin, $force, $pluginDirNamesWaitingToBeLoaded);
        }

        // add the plugin dir paths and require the plugin files
        $this->_mvc->addApplicationDirs($pluginDirName);

        $this->_loadPluginBootstrap($plugin);
        
        // This won't work unless the plugin has already been loaded.
        $plugin->setHasConfig((bool) $this->_broker->getHook($plugin->getDirectoryName(), 'config'));
        
        // remember that the plugin is loaded
        $plugin->setLoaded(true);
    }
    
    protected function _getDirectoryList()
    {
        // Construct the current list of potential, installed & active plugins
        require_once 'VersionedDirectoryIterator.php';
        
        // Loop through all the plugins in the plugin directory, 
        // and add each plugin directory name that has a plugin.php file 
        // to the list of all plugin directory names
        $dir = new VersionedDirectoryIterator($this->_basePath);
        $pluginDirNames = $dir->getValid();
        
        $fullPluginList = array();
        foreach($pluginDirNames as $pluginDirName) {
            if ($this->hasPluginFile($pluginDirName)) {
                $fullPluginList[] = $pluginDirName;
            }
        }
        return $fullPluginList;
    }
                    
    /**
     * Returns whether a plugin has a plugin.php file
     * 
     * @todo Factor into Plugin class (with setPluginPath() ?)
     * @param string $pluginDirName
     * @return boolean
     **/
    public function hasPluginFile($pluginDirName)
    {
        return file_exists($this->getPluginFilePath($pluginDirName));
    }
        
    /**
     * Returns the path to the plugin.php file
     * 
     * @todo Factor into Plugin record class?
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

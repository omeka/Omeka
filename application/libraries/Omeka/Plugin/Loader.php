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
     * An array of plugin directory names for plugins that have been installed (but not necessarily
     * activated and not necessarily have the plugin files).
     *
     * @var array
     **/
    protected $_installed = array();
    
    /**
     * An array of plugin directory names for currently activated plugins
     *
     * @var array
     **/
    protected $_active = array();
    
    /**
      * An array of all plugin directory names for plugins that have been loaded
      *
      * @var array
      **/
    protected $_loaded = array();
    
    /**
     * An array of all plugin directory names for plugins (installed or not) that are currently located
     * in the plugins/ directory
     *
     * @var array
     **/
    protected $_all = array();
            
    /**
      * A list of plugin directory names that have a new version of the plugin according to the plugin.ini file
      *
      * @var array
      **/
    protected $_has_new_version = array();
    
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
     */
    public function loadPlugins(array $plugins)
    {
        $this->_all = $this->_getDirectoryList();
        
        // Loop through the installed plugins and add the plugin directory names to a list. 
        // Add the directory names of upgradable plugins to a list.        
        // Finally, add the directory names of active plugin to a list.
        foreach ($plugins as $plugin) {            

            // Get the plugin directory name
            $pluginDirName = $plugin->name;
            if (in_array($pluginDirName, $this->_all)) {

                // Add the plugin directory name to the list of installed plugin directory names
                $this->setInstalled($pluginDirName);

                // If a plugin is active then store its plugin directory name in a list
                if ($plugin->active) {
                    $this->setActive($pluginDirName);
                }

                // If the plugin is upgradable, then store its directory name in a list
                if ($this->_iniReader->hasPluginIniFile($pluginDirName)) {
                    $pluginVersion = trim((string)$this->_iniReader->getPluginIniValue($pluginDirName, 'version'));
                    if ($pluginVersion && version_compare($pluginVersion, $plugin->version, '>')) {                
                        $this->_has_new_version[$pluginDirName] = $pluginDirName;
                    }
                }
            }             
        }
        
        foreach ($this->_active as $pluginDirName) {
            $this->load($pluginDirName);
        }        
    }
                
    /**
     * Loads a plugin (and make sure the plugin API is available)
     * 
     * To be loaded, the plugin must be installed, active, and does not have a newer version.
     * If loaded, the plugin will attempt to first load all plugins, both required and optional, that the plugin uses.  
     * However, it will not load a plugin that it uses, if that plugin is not installed and activated
     * 
     * @param string $pluginDirName The directory name of the plugin to load
     * @param boolean $force If true, throws exceptions if a plugin can't be 
     * loaded.
     * @param array $pluginDirNamesWaitingToBeLoaded The array of the directory names of dependent plugins waiting to be loaded
     * @return void
     **/
    public function load($pluginDirName, $force = false, $pluginDirNamesWaitingToBeLoaded = array())
    {   
        if ($force) {
            if (!$this->hasPluginFile($pluginDirName)) {
                throw new Exception("'$pluginDirName' plugin directory does not contain a 'plugin.php' file.");
            }
            if (!$this->isInstalled($pluginDirName)) {
                throw new Exception("'$pluginDirName' has not been installed.");
            }
            if (!$this->isActive($pluginDirName)) {
                throw new Exception("'$pluginDirName' has not been activated.");
            }
            if (!$this->_iniReader->meetsOmekaMinimumVersion($pluginDirName)) {
                throw new Exception("'$pluginDirName' requires a newer version of Omeka.");
            }
            if ($this->hasNewVersion($pluginDirName)) {
                throw new Exception("'$pluginDirName' must be upgraded to the new version before it can be loaded.");
            }
        }
        
        if (!($this->canLoad($pluginDirName) &&
            !in_array($pluginDirName, $pluginDirNamesWaitingToBeLoaded))) {
            return;
        }
                                     
        // add the current plugin to directory names waiting to be loaded
        $pluginDirNamesWaitingToBeLoaded[] = $pluginDirName;
        
        // load the required plugins for the plugin
        $requiredPluginDirNames = $this->_iniReader->getRequiredPluginDirNames($pluginDirName);
        if (count($requiredPluginDirNames) > 0) {
            foreach($requiredPluginDirNames as $requiredPluginDirName) {
                $this->load($requiredPluginDirName, $force, $pluginDirNamesWaitingToBeLoaded);
                
                // make sure the required plugin is loaded.
                // if a required plugin of the plugin cannot be loaded, 
                // then do not load the plugin
                if (!($this->isLoaded($requiredPluginDirName))) {
                    return;
                }
            }
        }

        // load the optional plugins for the plugin
        $optionalPluginDirNames = $this->_iniReader->getOptionalPluginDirNames($pluginDirName);
        if (count($optionalPluginDirNames) > 0) {
            foreach($optionalPluginDirNames as $optionalPluginDirName) {
                $this->load($optionalPluginDirName, $force, $pluginDirNamesWaitingToBeLoaded);
            }
        }

        // set the current plugin
        $this->_broker->setCurrentPluginDirName($pluginDirName);

        // add the plugin dir paths and require the plugin files
        $this->_mvc->addApplicationDirs($pluginDirName);
        $pluginFilePath = $this->getPluginFilePath($pluginDirName);
        require_once $pluginFilePath;

        // set the current plugin back to null
        $this->_broker->setCurrentPluginDirName(null);
        
        // remember that the plugin is loaded
        $this->_loaded[$pluginDirName] = $pluginDirName;
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
    public function canLoad($pluginDirName)
    {
        return $this->hasPluginFile($pluginDirName) &&
            $this->isInstalled($pluginDirName) && 
            $this->isActive($pluginDirName) &&
            $this->_iniReader->meetsOmekaMinimumVersion($pluginDirName) &&
            !$this->isLoaded($pluginDirName) &&
            !$this->hasNewVersion($pluginDirName);
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
     * Returns whether a plugin is active or not
     * 
     * @param string $pluginDirName
     * @return boolean
     **/
    public function isActive($pluginDirName)
    {
        return in_array($pluginDirName, $this->_active);
    }
    
    /**
     * Returns whether a plugin is installed or not
     * 
     * @param string $pluginDirName
     * @return boolean
     **/
    public function isInstalled($pluginDirName)
    {
        return in_array($pluginDirName, $this->_installed);
    }
    
    /**
     * Returns whether a plugin is loaded or not
     * 
     * @param string $pluginDirName
     * @return boolean
     **/
    public function isLoaded($pluginDirName)
    {
        return in_array($pluginDirName, $this->_loaded);
    }
    
    /**
     * Returns whether a plugin has a plugin.php file
     * 
     * @param string $pluginDirName
     * @return boolean
     **/
    public function hasPluginFile($pluginDirName)
    {
        return file_exists($this->getPluginFilePath($pluginDirName));
    }
        
    /**
     * Return an array of plugin directory names for the plugins that have not been installed yet
     *
     * @return array
     **/
    public function getNew()
    {
       return array_diff($this->_all, array_keys($this->_installed));
    }
    
    /**
     * Return whether a plugin has a newer version in the plugin.ini file than the version in the database.  
     *
     * @param string $pluginDirName
     * @return boolean
     **/
    public function hasNewVersion($pluginDirName)
    {
        return in_array($pluginDirName, $this->_has_new_version);        
    }
    
    /**
     * Returns an array of all of the plugin directory names in the plugin directory
     *
     * @return array
     **/
    public function getAll()
    {
        return $this->_all;
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
    
    public function setActive($pluginDirName)
    {
        $this->_active[$pluginDirName] = $pluginDirName;
    }
    
    public function setInstalled($pluginDirName)
    {
        $this->_installed[$pluginDirName] = $pluginDirName;
    }
    
    /**
     * Register the plugin broker so that plugin writers can use global functions
     * like add_plugin_hook() to interact with the plugin API.
     */
    public function registerPluginBroker()
    {
        Zend_Registry::set('pluginbroker', $this->_broker);
    }
}

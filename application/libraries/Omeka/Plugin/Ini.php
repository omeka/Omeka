<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Responsible for parsing the plugin.ini file for any given plugin.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Plugin_Ini
{
    protected $_pluginsRootDir;
        
    /**
     * @var array Set of Zend_Config_Ini objects corresponding to each plugin.
     */
    protected $_configs = array();
    
    public function __construct($pluginsRootDir)
    {
        $this->_pluginsRootDir = $pluginsRootDir;
    }
    
    /**
     * Returns a value in plugin.ini for a key
     *
     * Will return a null value if no value can be found in the ini file for the key.
     * 
     * @param string $pluginDirName
     * @param string $iniKeyName
     * @return null | string
     **/
    public function getPluginIniValue($pluginDirName, $iniKeyName)
    {
        // Extract the directory name from the plugin.
        if ($pluginDirName instanceof Plugin) {
            $pluginDirName = $pluginDirName->getDirectoryName();
        }
        
        $pluginIniPath = $this->getPluginIniFilePath($pluginDirName);
        if (file_exists($pluginIniPath)) {
            if (array_key_exists($pluginDirName, $this->_configs)) {
                $config = $this->_configs[$pluginDirName];
            } else {
                $config = new Zend_Config_Ini($pluginIniPath, 'info');
                $this->_configs[$pluginDirName] = $config;
            }
            
        } else {
    		throw new Exception("Path to plugin.ini for '$pluginDirName' is not correct.");
    	}
    	return $config->$iniKeyName;
    } 
                
    /**
     * Returns whether a plugin has a plugin.ini file
     * 
     * @param string $pluginDirName
     * @return boolean
     **/
    public function hasPluginIniFile($pluginDirName)
    {
        return file_exists($this->getPluginIniFilePath($pluginDirName));        
    }
    
    /**
     * Returns the path to the plugin.ini file
     * 
     * @param string $pluginDirName
     * @return string
     **/
    public function getPluginIniFilePath($pluginDirName)
    {
        if ($pluginDirName instanceof Plugin) {
            $pluginDirName = $pluginDirName->getDirectoryName();
        }
        return $this->_pluginsRootDir . DIRECTORY_SEPARATOR . $pluginDirName . DIRECTORY_SEPARATOR . 'plugin.ini';
    }
    
    public function load(Plugin $plugin)
    {
        // Can't really do anything if there is no plugin.ini file for this plugin.
        if (!$this->hasPluginIniFile($plugin)) {
            return;
        }
        
        $setters = array(
            'setDisplayName'            => 'name',
            'setAuthor'                 => 'author',
            'setDescription'            => 'description',
            'setLinkUrl'                => 'link',
            'setMinimumOmekaVersion'    => 'omeka_minimum_version',
            'setTestedUpToOmekaVersion' => 'omeka_tested_up_to',
            'setIniVersion'             => 'version',
            'setRequiredPlugins'        => 'required_plugins',
            'setOptionalPlugins'        => 'optional_plugins',
            'setIniTags'                => 'tags'
        );
                
        foreach ($setters as $method => $iniField) {
            $plugin->$method($this->getPluginIniValue($plugin, $iniField));
        }
    }
}

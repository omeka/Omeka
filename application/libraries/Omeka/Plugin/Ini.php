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
     * An associative array of all plugin directory names for plugins that are used optionally by another plugin.
     * The key is a pluginDirName and the value is an array of the plugin directory names of its optional plugins
     *
     * @var array
     **/
    protected $_optional = array();
    
    /**
      * An associative array of all plugin directory names for plugins that are required by another plugin.
      * The key is a pluginDirName and the value is an array of the plugin directory names of its required plugins
      *
      * @var array
      **/
    protected $_required = array();
    
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
     * Returns an array of the plugin directory names for the plugins that the plugin requires
     * 
     * @param string $pluginDirName
     * @return array
     **/
    public function getRequiredPluginDirNames($pluginDirName)
    {
        if ($this->_required[$pluginDirName] == null) {            
            $this->_required[$pluginDirName] = array();
            if ($this->hasPluginIniFile($pluginDirName)) {            
                $rrPluginDirNames = explode(',', trim((string)$this->getPluginIniValue($pluginDirName, 'required_plugins')));
                if(count($rrPluginDirNames) == 1 && trim($rrPluginDirNames[0]) == '') {
                    $rPluginDirNames = array();
                } else {
                    $rPluginDirNames = array();
                    foreach($rrPluginDirNames as $rrPluginDirName) {
                        $rPluginDirNames[] = trim($rrPluginDirName);
                    }
                }
                $this->_required[$pluginDirName] = $rPluginDirNames;
            }
        }

        return $this->_required[$pluginDirName];
    }
    
    /**
     * Returns an array of the plugin directory names for the plugins that the plugin optionally uses
     * 
     * @param string $pluginDirName
     * @return array
     **/
    public function getOptionalPluginDirNames($pluginDirName)
    {
        if ($this->_optional[$pluginDirName] == null) {
            $this->_optional[$pluginDirName] = array();
            if ($this->hasPluginIniFile($pluginDirName)) {
                $ooPluginDirNames = explode(',', trim((string)$this->getPluginIniValue($pluginDirName, 'optional_plugins')));
                if(count($ooPluginDirNames) == 1 && trim($ooPluginDirNames[0]) == '') {
                    $oPluginDirNames = array();
                } else {
                    $oPluginDirNames = array();
                    foreach($ooPluginDirNames as $ooPluginDirName) {
                        $oPluginDirNames[] = trim($ooPluginDirName);
                    }
                }
                $this->_optional[$pluginDirName] = $oPluginDirNames;
            }
        }

        return $this->_optional[$pluginDirName];
    }
    
    /**
     * Returns whether the current version of Omeka is greater than or equal to the 
     * minimum version required by the plugin.
     * 
     * @param string $pluginDirName
     * @return bool
     **/
    public function meetsOmekaMinimumVersion($pluginDirName)
    {
        $meetsOmekaMinimumVersion = true;
        
        if ($this->hasPluginIniFile($pluginDirName)) {
            $omekaMinimumVersion = (string)$this->getPluginIniValue($pluginDirName, 'omeka_minimum_version');
            if (trim($omekaMinimumVersion) != '' && version_compare($omekaMinimumVersion, OMEKA_VERSION, '>')) {        
                $meetsOmekaMinimumVersion = false;            
            }
        }
        
        return $meetsOmekaMinimumVersion;
    }
    
    /**
     * Returns whether the current version of Omeka is greater than or equal to the 
     * minimum version required by the plugin.
     * 
     * @param string $pluginDirName
     * @return bool
     **/
    public function meetsOmekaTestedUpTo($pluginDirName)
    {
        $meetsOmekaTestedUpTo = true;
        
        if ($this->hasPluginIniFile($pluginDirName)) {
            $omekaTestedUpTo = (string)$this->getPluginIniValue($pluginDirName, 'omeka_tested_up_to');
            if (trim($omekaTestedUpTo) != '' && version_compare($omekaTestedUpTo, OMEKA_VERSION, '<')) {        
                $meetsOmekaTestedUpTo = false;            
            }
        }
        
        return $meetsOmekaTestedUpTo;
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
        return $this->_pluginsRootDir . DIRECTORY_SEPARATOR . $pluginDirName . DIRECTORY_SEPARATOR . 'plugin.ini';
    }
}

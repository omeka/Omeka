<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Responsible for parsing the plugin.ini file for any given plugin.
 * 
 * @package Omeka\Plugin
 */
class Omeka_Plugin_Ini
{
    /**
     * Plugins directory.
     *
     * @var string
     */
    protected $_pluginsRootDir;

    /**
     * Set of Zend_Config_Ini objects corresponding to each plugin.
     *
     * @var array
     */
    protected $_configs = array();

    /**
     * @param string $pluginsRootDir Plugins directory.
     */
    public function __construct($pluginsRootDir)
    {
        $this->_pluginsRootDir = $pluginsRootDir;
    }

    /**
     * Retrieve a value in plugin.ini for a given key.
     *
     * Will return a null value if no value can be found in the ini file for the
     * key.
     *
     * @param string $pluginDirName Plugin name.
     * @param string $iniKeyName INI key to retrieve.
     * @return string|null Retrieved INI value (null if not found).
     */
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
            throw new RuntimeException(__('Path to plugin.ini for "%s" is not correct.', $pluginDirName));
        }
        return $config->$iniKeyName;
    }

    /**
     * Return whether a plugin has a plugin.ini file
     *
     * @param string $pluginDirName Plugin name.
     * @return boolean
     */
    public function hasPluginIniFile($pluginDirName)
    {
        return file_exists($this->getPluginIniFilePath($pluginDirName));
    }

    /**
     * Return the path to the plugin.ini file
     *
     * @param string $pluginDirName Plugin name.
     * @return string
     */
    public function getPluginIniFilePath($pluginDirName)
    {
        if ($pluginDirName instanceof Plugin) {
            $pluginDirName = $pluginDirName->getDirectoryName();
        }
        return $this->_pluginsRootDir . '/' . $pluginDirName . '/' . 'plugin.ini';
    }

    /**
     * Initialize a Plugin model object with the values from the INI file.
     *
     * @param Plugin $plugin The plugin model to initialize.
     * @return void
     */
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
            'setIniTags'                => 'tags',
            'setSupportLinkUrl'         => 'support_link'
        );

        foreach ($setters as $method => $iniField) {
            $plugin->$method($this->getPluginIniValue($plugin, $iniField));
        }
    }
}

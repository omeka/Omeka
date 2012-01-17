<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Responsible for parsing the plugin.ini file for any given plugin.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
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
            throw new Exception("Path to plugin.ini for '$pluginDirName' is not correct.");
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
            'setRequireOnce'            => 'require_once',
        );

        foreach ($setters as $method => $iniField) {
            $plugin->$method($this->getPluginIniValue($plugin, $iniField));
        }
    }
}

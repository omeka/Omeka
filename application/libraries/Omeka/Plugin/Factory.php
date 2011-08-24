<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Responsible for creating a set of Plugin records corresponding to plugins 
 * that have not been installed yet.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Plugin_Factory
{
    /**
     * Base path for plugins; the plugin directory
     *
     * @var string
     */
    protected $_basePath;
    
    /**
     * @param string $basePath Plugin base directory.
     */
    public function __construct($basePath)
    {
        $this->_basePath = $basePath;
    }
    
    /**
     * Retrieve all new plugins in the plugin directory.
     *
     * @param array $existingPlugins An array of existing Plugin objects.
     * @return array An array of Plugin objects for the new plugins.
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
     * Retrieve an array of all the plugins in the plugin directory.
     * A plugin is considered to be present when a directory includes a
     * plugin.php file.
     *
     * @return array A list of valid plugin directory names.
     */
    protected function _getDirectoryList()
    {
        // Construct the current list of potential, installed & active plugins
        require_once 'VersionedDirectoryIterator.php';
        
        // Loop through all the plugins in the plugin directory, 
        // and add each plugin directory name that has a plugin.php file 
        // to the list of all plugin directory names
        $dir = new VersionedDirectoryIterator($this->_basePath);
        return $dir->getValid();
    }
}

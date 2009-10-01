<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Responsible for creating a set of Plugin records corresponding to plugins 
 * that have not been installed yet.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Plugin_Factory
{
    protected $_basePath;
    
    public function __construct($basePath)
    {
        $this->_basePath = $basePath;
    }
        
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

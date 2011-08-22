<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @subpackage Models
 * @author CHNM
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class PluginTable extends Omeka_Db_Table
{
    protected $_target = 'Plugin';
    
    public function findAllWithIniFiles()
    {
        // This loops through all the records in the plugins table, and updates the version for each plugin
        $plugins = $this->findAll();
        $pluginsWithIniFiles = array();
        foreach($plugins as $plugin) {
            $path = PLUGIN_DIR . '/' . $plugin->name . '/plugin.ini';
            if (file_exists($path)) {
                $pluginsWithIniFiles[] = $plugin;
            } 
        }
        return $pluginsWithIniFiles;
    }
    
    public function findByDirectoryName($pluginDirName)
    {
        $select = $this->getSelect()->where("p.name = ?")->limit(1);        
        return $this->fetchObject($select, array($pluginDirName));        
    }
    
}

<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @subpackage Models
 * @author CHNM
 **/
class PluginTable extends Omeka_Db_Table
{
    protected $_target = 'Plugin';
    
    public function findAllWithIniFiles()
    {
        // This loops through all the records in the plugins table, and updates the version for each plugin
        $plugins = $this->findAll();
        $pluginsWithIniFiles = array();
        foreach($plugins as $plugin) {
            $path = PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin->name . DIRECTORY_SEPARATOR . 'plugin.ini';
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

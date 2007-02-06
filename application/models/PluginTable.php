<?php
require_once LIB_DIR.DIRECTORY_SEPARATOR.'Kea'.DIRECTORY_SEPARATOR.'Plugin.php';
/**
 * Special Plugin finder (to generate the correct plugins from the db)
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class PluginTable extends Doctrine_Table
{
	public function findActive() {
		return $this->findBySql("active = 1");
	}
	
	public function activeArray($router) {
		$records = $this->findActive();
		$plugins = array();
		foreach( $records as $record )
		{
			$name = $record->name;
			require_once $record->path;
			$plugins[] = new $name($router, $record);
		}
		return $plugins;
	}
	
	public function getNewPluginNames() {
		$plugins = $this->findAll();
		$plugin_dirs = new DirectoryIterator(PLUGIN_DIR);
		$new_plugins = array();
				
		foreach( $plugin_dirs as $v )
		{
			$dir = $v->__toString();
			if(!$v->isDot() && $v != '.svn') {
				$new_plugins[$dir] = $dir;
			}
			foreach( $plugins as $plugin )
			{
				if($dir == $plugin->name) 
				{
					unset($new_plugins[$dir]);
				}
			}
		}
		return $new_plugins;		
	}
} // END class PluginTable extends Doctrine_Table
?>
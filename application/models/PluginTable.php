<?php
require_once LIB_DIR.DIRECTORY_SEPARATOR.'Kea'.DIRECTORY_SEPARATOR.'Plugin.php';
/**
 * Special Plugin finder (to generate the correct plugins from the db)
 *
 * @package Omeka
 * 
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
			require_once PLUGIN_DIR.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.'.php';
			$plugins[] = new $name($router, $record);
		}
		return $plugins;
	}

	public function installNew() {
		//Installation will need to create new tables
		Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_CREATE_TABLES, true);
		
		$plugins = $this->findAll();
		$pluginDirs = new DirectoryIterator(PLUGIN_DIR);
		$newPlugins = array();
		
		//Pull a list of the new ones		
		foreach( $pluginDirs as $v )
		{
			$dir = $v->__toString();
			if(!$v->isDot() && $v != '.svn' && $v->isDir()) {
				$newPlugins[$dir] = $dir;
			}
			foreach( $plugins as $plugin )
			{
				if($dir == $plugin->name) 
				{
					unset($newPlugins[$dir]);
				}
			}
		}
		
		foreach ($newPlugins as $key => $name) {
			$path = PLUGIN_DIR.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$name.'.php';
			require_once $path;
			$plugin = new $name();
			$plugin->install();
		}
	}
} // END class PluginTable extends Doctrine_Table
?>
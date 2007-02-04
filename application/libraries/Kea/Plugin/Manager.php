<?php
/**
 * Kea_Plugin_Manager
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Kea_Plugin_Manager
{
	private $plugins;
	private $table;
	private $debug = FALSE;
	
	private static $_instance;
	private static $path = PLUGIN_PATH;
	
	private function __construct() {
		$this->table = Doctrine_Manager::getInstance()->connection()->getTable('Plugin');
	}
	
	public static function getInstance() {
		if(empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function debug($flag = null) {
		if($flag !== null) {
			$this->debug = (bool) $flag;
		}
		else return $this->debug;
	}
	
	public function getPlugins() {
		return $this->plugins;
	}
	
	public function getPlugin($name) {
		return $this->plugins[$name];
	}
	
	public function getPluginPath() {
		return self::$path;
	}
	
	public function notify( $msg, &$obj, $params) {
		if($this->debug) echo 'Message: '.$msg.' has been fired<br/>';
		
		$results = array();
		if(!empty($this->plugins))
		{
			foreach( $this->plugins as $plugin )
			{
				$result = $plugin->update( $msg, $obj, $params);
				if($result) $results[$plugin->name] = $result;
			}			
		}
		return !empty($results) ? $results : false;
	}
	
	/**
	 * Load all active plugins from the database
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	public function loadActive() 
	{
		$table = $this->table;
		$plugins = $table->findBySql('active = 1');
		$plugin_a = array(); 
		foreach( $plugins as $plugin )
		{
			require_once $plugin->path;
			$plugin_a[$plugin->name] = $table->createByName($plugin->toArray(), $plugin->name);
			$plugin_a[$plugin->name]->manager($this);
		}
		$this->plugins = $plugin_a;
	}
			
	public function install( $plugin ) 
	{
		if(is_string($plugin)) { $plugin =  new $plugin(); }
		if(!$plugin->isInstalled()) {
			if(!$plugin->install())
			{
				throw new Exception( 'Could not load plugin called \'' . get_class($plugin) );
			}
		}
		$plugin->manager($this);
		$this->plugins[get_class($plugin)] = $plugin;
		return true;
	}

} // END class Kea_Plugin_Manager

?>
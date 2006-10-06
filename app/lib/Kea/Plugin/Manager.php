<?php
/**
 * undocumented class
 *
 * @package default
 * @author Kris Kelly
 **/
class Kea_Plugin_Manager
{
	private static $_plugins = array();
	
	private static $_instance;
	
	/**
	 * Singleton pattern
	 */
	private function __construct() {}
	private function __clone() {}


	/**
	 * Returns, or creates and returns, the Kea_Plugin_Manager object
	 * @return Kea_Plugin_Manager object
	 */
	public static function getInstance()
	{
		if( !self::$_instance instanceof self ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function run()
	{
		return self::getInstance()->loadAll();
	}
	
	public static function loadAll()
	{
		return self::getInstance()->_loadAll();
	}
	
	private function _loadAll()
	{
		//Connect to database and/or read a directory listing to load plugin names that have been installed
		/*
		$array = read from a database somewhere
		*/
		$array = array( array('plugin' => new Kea_Plugin_Test(), 'installed' => 1) );
		//self::$_plugins = $test;
		foreach($array as $k=>$v)
		{
			if ( $v['installed'] )
			{
				self::attach($v['plugin']);
			}
		}
		return $this;
	}
	
	public static function install( Kea_Plugin_Interface $plugin )
	{
		if( !array_key_exists( get_class($plugin), self::$_plugins ) && $plugin->install() ) self::getInstance()->attach( $plugin );
	}
	
	private function attach( Kea_Plugin_Interface $plugin ) 
	{
		self::$_plugins[ get_class($plugin) ] = $plugin;
	}
	
	public static function detach( Kea_Plugin_Interface $plugin ) {}
	
	public static function notify( Kea_Plugin_Message $msg ) 
	{	
		$newMsg = $msg;
		foreach(self::$_plugins as $plugin)
		{
			//var_dump($plugin);
			$newMsg = $plugin->update( self::getInstance() , $newMsg);
	/*		foreach( $msg->getMethods() as $key => $value )
			{
				echo get_class($plugin) . ' has been notified of the call to '. $msg->getController() . '->' . $value;
			}
	*/		
		}
		return ($newMsg) ? $newMsg : $msg;
	}
	
	public static function getActivePlugins()
	{
		foreach(self::$_plugins as $name=>$obj)
		{
			echo $name;
		}
	}
} // END class 


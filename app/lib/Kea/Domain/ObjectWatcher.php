<?php

class Kea_Domain_ObjectWatcher
{
	private $all = array();
	private $dirty = array();
	private $delete = array();
	private static $instance;
	
	private function __construct() {}
	
	public static function instance()
	{
		if(!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function globalKey( Kea_Domain_Model $obj )
	{
		return get_class( $obj ) . '.' . $obj->getId();
	}
	
	public static function add( Kea_Domain_Model $obj )
	{
		$inst = self::instance();
		$inst->all[$inst->globalKey( $obj )] = $obj;
	}
	
	public static function exists( $classname, $id )
	{
		$inst = self::instance();
		$key = "$classname.$id";
		if( array_key_exists( $key, $inst->all ) ) {
			return $inst->all[$key];	
		} else {
			return false;
		}
	}

}

?>
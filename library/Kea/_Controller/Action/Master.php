<?php

class Kea_Controller_Action_Master
{
	private static $_instance;
	private $_controllers = array();
	private $_controller_dir;

	private function __construct()
	{
		$this->_controller_dir = ABS_CONTROLLER_DIR;
	}
	
	private function __clone() {}
	private function __get( $arg ) {}
	private function __set( $arg, $val ) {}
	
	public static function instance()
	{
		if( !self::$_instance instanceof Kea_Action_Master ) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	private function _controllerExists( $controller_name )
	{
		if( array_key_exists( $controller_name, $this->_controllers ) ) {
			return $this->_controllers[$controller_name];
		}
		return false;
	}
	
	public function __call( $controller_name, $args )
	{
		if( $controller = $this->_controllerExists( $controller_name ) ) {
			return $controller;
		}
		
		$controller_file = $this->_controller_dir . DS . strtolower( $controller_name ) . '_controller.php';

		if( file_exists( $controller_file ) ) {
			require_once( $controller_file );
			$controller_class = ucfirst( $controller_name ) . 'Controller';
			$this->_controllers[$controller_name] = new $controller_class;
			return $this->_controllers[$controller_name];
		} else {
			throw new Kea_Action_Exception(
				'No controller by the name of ' . $controller_name . ' exists.'
			);
		}
	}
}
?>
<?php

abstract class Kea_Controller_Base
{

	protected static $_request;
	
	protected static $_session;
	
	protected static $_route;
	
	protected static $_plugin_manager;
	
	public function request()
	{
		return self::$_request;
	}
	
	public function session()
	{
		return self::$_session;
	}
	
	public function route()
	{
		return self::$_route;
	}
	
	public function plugins()
	{
		return (self::$_plugin_manager) ? self::$_plugin_manager : null;
	}
}

?>
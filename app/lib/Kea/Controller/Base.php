<?php

abstract class Kea_Controller_Base
{

	protected static $_request;
	
	protected static $_session;
	
	protected static $_route;
	
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

}

?>
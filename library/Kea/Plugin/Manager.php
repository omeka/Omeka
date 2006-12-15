<?php

class Kea_Plugin_Manager
{
	public function __construct() {}
	
	public function getActivated()
	{
		return array(array('type' => 'public', 'title' => 'foo'));
	}
}

?>
<?php

/**
 * Kea_Plugin_Interface
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
interface Kea_Plugin_Interface
{
	public function update($msg, &$obj, $params);
	public function install(array $config);
	public function isInstalled();
} // END interface Kea_Plugin_Interface

?>
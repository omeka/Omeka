<?php
/**
 * undocumented class
 *
 * @package default
 * @author Kris Kelly
 **/
interface Kea_Plugin_Interface
{
	function update( Kea_Plugin_Manager $mgr, Kea_Plugin_Message &$msg);
	
	/**
	 * Installs the plugin
	 *
	 * @return bool  TRUE on successfull install or if already installed, FALSE on failure
	 * @author Kris Kelly
	 **/
	function install();
	
	function uninstall();
} // END interface Kea_Plugin_Interface

?>
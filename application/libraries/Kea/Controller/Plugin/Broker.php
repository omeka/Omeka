<?php
/** Zend_Controller_Plugin_Broker */
require_once 'Zend/Controller/Plugin/Broker.php';
/**
 * customized plugin broker
 *
 * @package Sitebuilder
 * 
 **/
class Kea_Controller_Plugin_Broker extends Zend_Controller_Plugin_Broker
{
	
	private static $_instance;
	
	/**
     * Singleton instance
     * 
     * @return Kea_Controller_Plugin_Broker
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
	
	private function __construct() {}

	public function plugins() {
		return $this->_plugins;
	}
	
	public function header() {
		foreach( $this->_plugins as $plugin )
		{
			$plugin->header();
		}
	}
	
	public function footer() {
		foreach( $this->_plugins as $plugin )
		{
			$plugin->footer();
		}
	}
} // END class Kea_Controller_Plugin_Broker

?>
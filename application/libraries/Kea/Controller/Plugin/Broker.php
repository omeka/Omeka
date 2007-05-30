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
	
	private $_binded = array();
	
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
	
	public function addBound($class, $bound) {
		$this->_binded[$class][] = $bound;
	}
	
	public function getBound($class) {
		if(array_key_exists($class, $this->_binded)) {
			return $this->_binded[$class];
		} else {
			return array();
		}
		
	}
	
	/**
	 * This applies to all plugin hooks that are defined in Kea_Plugin
	 *
	 * @return array|void
	 **/
	public function __call($m, $a) {
		$retVals = array();
		foreach( $this->_plugins as $key => $plugin )
		{
			$retVal = call_user_func_array(array($plugin, $m), $a);
			if($retVal !== null) $retVals[$key] = $retVal;
		}
		if(!empty($retVals)) return $retVals;
	}
} // END class Kea_Controller_Plugin_Broker

?>
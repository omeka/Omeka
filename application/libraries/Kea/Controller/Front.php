<?php
require_once 'Zend/Controller/Front.php';
require_once 'Kea/Controller/Plugin/Broker.php';
/**
 * customized Zend Front Controller
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Kea_Controller_Front extends Zend_Controller_Front
{
	
	/**
     * Singleton instance
     * @var self 
     */
    private static $_instance = null;
	
	private function __construct()
    {
        $this->_plugins = Kea_Controller_Plugin_Broker::getInstance();
    }

	public function getPlugins() 
	{
		return $this->_plugins->plugins();
	}
	
	/**
     * Singleton instance
     * 
     * @return Kea_Controller_Front
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
} // END class Kea_Controller_Front extends Zend_Controller_Front

?>
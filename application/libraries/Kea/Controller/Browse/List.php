<?php
require_once 'Kea/Controller/Action.php';
require_once 'Kea/Controller/Browse/Interface.php';
/**
 * In charge of paginated browsing for the controllers
 *
 * @package Omeka
 **/
class Kea_Controller_Browse_List implements Kea_Controller_Browse_Interface
{
	protected $_class;
	protected $_controller;
	
	public function __construct($class, Kea_Controller_Action $controller, array $options = array() )
	{
		$this->_class = $class;
		$this->_controller = $controller;
	}
	
	public function browse()
	{	
		$pluralName = strtolower($this->_class).'s';
		$viewPage = $pluralName.DIRECTORY_SEPARATOR.'browse.php';
		$$pluralName = Doctrine_Manager::getInstance()->getTable($this->_class)->findAll();
		$this->_controller->render($viewPage, compact($pluralName));
	}
}
?>
<?php
require_once 'Kea/Controller/Action.php';
require_once 'Kea/Controller/Browse/Interface.php';
/**
 * This is for comprehensive listing of records a-la table-based list
 *
 * @package Omeka
 **/
class Kea_Controller_Browse_List implements Kea_Controller_Browse_Interface
{
	protected $_class;
	protected $_controller;
	
	/**
	 * Kea_Controller_Search
	 *
	 * @var Kea_Controller_Search
	 **/
	protected $_search;
	
	public function __construct($class, Kea_Controller_Action $controller, array $options = array() )
	{
		$this->_class = $class;
		$this->_controller = $controller;
		$this->_search = new Kea_Controller_Search($class);
	}
	
	public function browse()
	{	
		$pluralName = strtolower($this->_class).'s';
		$viewPage = $pluralName.DIRECTORY_SEPARATOR.'browse.php';
		
		if($terms = $_REQUEST['search']) {
			$this->_search->terms = $terms;
			$$pluralName = $this->_search->run();
			var_dump( $$pluralName->count() );
		}else {
			$$pluralName = Doctrine_Manager::getInstance()->getTable($this->_class)->findAll();
		}
		
		$this->_controller->render($viewPage, compact($pluralName));
	}
}
?>
<?php
require_once 'Kea/Controller/Action.php';
require_once 'Kea/Controller/Browse/Interface.php';
/**
 * This is for comprehensive listing of records a-la table-based list
 *
 * @package Omeka
 **/
class Kea_Controller_Browse_List extends Kea_Controller_Browse_Abstract
{
	public function browse()
	{	
		$pluralName = $this->formatPluralized();
		$viewPage = $pluralName.DIRECTORY_SEPARATOR.'browse.php';
		
		$query = $this->getQuery();
		if(isset($_REQUEST['search'])) {
			$terms = $_REQUEST['search'];
			$this->_search->terms = (!empty($query) ? $query : $terms);
			$$pluralName = $this->_search->run();
		} else {
			if(!empty($query)) {
				$$pluralName = $query->execute();
			} else {
				$$pluralName = Doctrine_Manager::getInstance()->getTable($this->_class)->findAll();
			}
		}
		
		$this->_controller->render($viewPage, compact($pluralName));
	}
}
?>
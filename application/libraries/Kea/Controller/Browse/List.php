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
		
		Kea_Controller_Plugin_Broker::getInstance()->filterBrowse($this);
		
		if(!$this->isCustomQuery()) {
			$$pluralName = Doctrine_Manager::getInstance()->getTable($this->_class)->findAll();
		} else {
			$$pluralName = $this->buildQuery()->execute();
		}
		$totalVar = 'total_'.$pluralName;
		
		$$totalVar = count($$pluralName);
		
		Zend::Register($pluralName, $$pluralName);
		return $this->_controller->render($viewPage, compact($pluralName,$totalVar));
	}
}
?>
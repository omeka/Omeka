<?php
/**
 * @package Omeka
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Collection.php';
require_once 'Kea/Controller/Action.php';
class CollectionsController extends Kea_Controller_Action
{
	public function init()
	{
		$this->_table = $this->getTable('Collection');
		$this->_modelClass = 'Collection';
	}
	
	public function browseAction()
	{
		$dql = "SELECT c.* FROM Collection c";
		
		if(!$this->isAllowed('showInactive')) {
			$dql .= " WHERE c.public = 1";
		}
		
		$q = new Doctrine_Query;
		$q->parseQuery($dql);
		
		$collections = $q->execute();
		
		$total_results = count($collections);
		
		$total_collections = $total_results;
		Zend::register('total_collections', $total_results);
		Zend::register('total_results', $total_results);
		
		fire_plugin_hook('browse_collections', $collections);
		
		return $this->render('collections/browse.php', compact('collections','total_collections'));
	}
}
?>
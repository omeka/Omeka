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
		$this->_table = Doctrine_Manager::getInstance()->getTable('Collection');
		$this->_modelClass = 'Collection';
	}
	
	protected function commitForm($collection)
	{
		if(!empty($_POST)) {
			
			//Handle the boolean vars in the form
			//This must be a radio button b/c checkboxes don't submit post correctly
			if(array_key_exists('active', $_POST)) {
				$collection->active = (bool) $_POST['active'];
				unset($_POST['active']);
			}
				
			if(array_key_exists('featured', $_POST)) {
				$collection->featured = (bool) $_POST['featured'];
				unset($_POST['featured']);
			}	
				
		}
		return parent::commitForm($collection);
		
	}
	
	public function browseAction()
	{
		$dql = "SELECT c.* FROM Collection c";
		
		if(!$this->isAllowed('showInactive')) {
			$dql .= " WHERE c.active = 1";
		}
		
		$q = new Doctrine_Query;
		$q->parseQuery($dql);
		
		$collections = $q->execute();
		
		$total_results = count($collections);
		
		$total_collections = $total_results;
		Zend::register('total_collections', $total_results);
		Zend::register('total_results', $total_results);
		
		return $this->render('collections/browse.php', compact('collections','total_collections'));
	}
}
?>
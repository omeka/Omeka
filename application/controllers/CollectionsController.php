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
	
	protected function preCommitForm($collection)
	{
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
		
		return true;
	}
	
	protected function postCommitForm($collection)
	{
		//Process the collectors that have been provided on the form
		$collectorsPost = $_POST['collectors'];
		
		foreach ($collectorsPost as $k => $c) {
			
			//Numbers mean that an entity_id has been passed, so add the relation
			if(is_numeric($c)) {
				$entity_id = $c;
				$collection->addRelatedIfNotExists($entity_id, 'collector');
			}else {
				//@todo Add support for entering a string name (this is thorny b/c of string splitting and unicode)
				throw new Exception( 'Cannot enter a collector by name.' );
			}
		}
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
		
		$this->pluginHook('onBrowseCollections', array($collections));
		
		return $this->render('collections/browse.php', compact('collections','total_collections'));
	}
}
?>
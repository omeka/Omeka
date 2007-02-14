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
	}
	
	/**
	 * All the code below this marker is essentially a direct duplication of the working code in the ItemsController,
	 * where Item has been changed to Collection.  It may be worthwhile to abstract this basic CRUD interface to the Kea_Controller_Action class
	 * as a builtin form of scaffolding that may be overriden as needed
	 */
	
    public function indexAction()
    {
		$this->_forward('collections', 'browse');
    }
	
	public function showAction()
	{
		try{
			$collection = $this->findById();
		}catch(Exception $e) {
			echo $e->getMessage();exit;
		}
		
		$this->render('collections/show.php', compact('collection'));
	}
	
	public function browseAction()
	{
		$collections = $this->_table->findAll();
		
		$this->render('collections/browse.php', compact('collections'));
	}
	
	public function addAction()
	{
		$collection = new Collection();
		if($this->commitForm($collection))
		{
			$this->_redirect('collections/browse/');
		}else {
			$errors = $collection->getErrorStack();
			$this->render('collections/add.php', compact('collection', 'errors'));
		}
	}
	
	public function editAction()
	{
		try{
			$collection = $this->findById();
		}catch(Exception $e) {
			echo $e->getMessage();exit;
		}
		
		if($this->commitForm($collection))
		{
			$this->_redirect('collections/show/'.$collection->id);
		}else{
			$errors = $collection->getErrorStack();
			$this->render('collections/edit.php', compact('collection', 'errors'));
		}
		
	}
	
	private function commitForm($collection)
	{
		if(!empty($_POST))
		{
			$collection->setArray($_POST);
			try {
				$collection->save();
				return true;
			}
			catch(Doctrine_Validator_Exception $e) {
				return false;
			}	
		}
		return false;
	}
	
    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>
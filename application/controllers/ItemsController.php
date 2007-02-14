<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
/**
 * @package Omeka
 * @author Nate Agrin, Kris Kelly
 **/
require_once 'Kea/Controller/Action.php';
class ItemsController extends Kea_Controller_Action
{	
	protected $_protected = array('browse');
	
	public function init() 
	{
		$this->_table = Doctrine_Manager::getInstance()->getTable('Item');
	}
	
    public function indexAction()
    {
		$this->browseAction();
    }

	public function browseAction()
	{
		//Should be mutable, possibly a POST variable with a default stored in a config file
		$per_page = 2;
		
		$page = $this->getRequest()->getParam('page');
		if(!$page) $page = 1;
		
		$offset = ($page - 1) * $per_page;
		
		$table = Doctrine_Manager::getInstance()->getTable('Item');
		
		$items = $table->createQuery()
				   		->limit($per_page)
				   		->offset($offset)
				   		->execute();
		
		$total = $table->count();
				
		$this->render("items/browse.php", compact("total", "offset", "items", "per_page", "page"));
	}

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
	
	public function addAction()
	{
		$item = new Item();
		if($this->commitForm($item))
		{
			$this->_redirect('items/browse/');
		}else {
			$this->render('items/add.php', compact('item'));
		}
	}
	
	public function editAction()
	{
		$item = $this->findById();
		if($this->commitForm($item))
		{
			$this->_redirect('items/show/'.$item->id);
		}else{
			$this->render('items/edit.php', compact('item'));
		}
	}
	
	private function commitForm($item)
	{
		if(!empty($_POST))
		{
			$item->setArray($_POST);
			try {
				$item->save();
				return true;
			}
			catch(Doctrine_Validator_Exception $e) {
				return false;
			}	
		}
		return false;
	}
	
	public function showAction() 
	{
		// This is abstracted out in the Kea_Controller_Action class
		$item = $this->findById();
		
		if(!empty($_POST['tags'])) $this->addTags($item);
		echo $this->render('items/show.php', compact("item"));
	}

	private function addTags($item) {
		/* TODO: catch the current User somewhere in here, shoot it to the addTagString() method */
		if($tagString = $_POST['tags']) {
			$item->addTagString($tagString);			
			try{
				$item->save();
				$item->refresh();
			//This error processing part should be abstracted out to the individual models, at least if all we want to do is display the errors
			}catch(Doctrine_Validator_Exception $e) {
				$item->gatherErrors($e);
			}
		}
	}
}
?>
<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
/**
 * @todo customize the Search query to include featured/not featured, plus whatever else we can think of
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class ItemsController extends Kea_Controller_Action
{	
	protected $_protected = array('browse', 'delete');
	
	public function init() 
	{
		$this->_table = Doctrine_Manager::getInstance()->getTable('Item');
		$this->_modelClass = 'Item';
		$this->_browse = new Kea_Controller_Browse_Paginate('Item', $this);
	}
	
	/**
	 * @todo Browse should be able to narrow by Collection, Type, Tag, etc.
	 *
	 * @return void
	 **/
	public function browseAction()
	{	
		$query = $this->_browse;
		//replace with permissions check
		if(!$this->getRequest()->getParam('admin')) {
			$query->where('items.active = 1');
		} 

		//filter based on tags
		if($tag = $this->_getParam('tag')) {
			$query->join('items_tags ON items_tags.item_id = items.id JOIN tags ON tags.id = items_tags.tag_id');
			$query->where('tags.name = :tagName');
			$query->addParam('tagName', $tag);
		}
		
		$this->_browse->browse();
	}
	
	/**
	 * Processes and saves the form to the given record
	 *
	 * @param Kea_Record
	 * @return boolean True on success, false otherwise
	 **/
	protected function commitForm($item)
	{
		if(!empty($_POST))
		{
			
			$item->setFromForm($_POST);
					
			if(!empty($_POST['change_type'])) return false;
			
			if(!empty($_FILES["file"]['name'][0])) {
				//Handle the file uploads
				foreach( $_FILES['file']['error'] as $key => $error )
				{ 
					try{
						$file = new File();
						$file->upload('file', $key);
						$item->Files->add($file);
					}catch(Exception $e) {
						echo $e->getMessage();
						$file->delete();
						return false;
					}
				
				}
			}
			
			if($_POST['public']) $item->public = 1;
			
			try {
				$item->save();
				return true;
			}
			catch(Doctrine_Validator_Exception $e) {
				$item->gatherErrors($e);
				return false;
			}	
		}
		return false;
	}
	
	/**
	 * Get all the collections and all the active plugins for the form
	 *
	 * @return void
	 **/
	protected function loadFormData() 
	{
		$collections = Doctrine_Manager::getInstance()->getTable('Collection')->findAll();
		$plugins = Doctrine_Manager::getInstance()->getTable('Plugin')->findActive();
		$types = Doctrine_Manager::getInstance()->getTable('Type')->findAll();
		
		$this->_view->assign(compact('collections', 'plugins', 'types'));
	}
	
	public function showAction() 
	{
		$item = $this->findById();
	
		$user = Doctrine_Manager::getInstance()->getTable('User')->find(1);

		if($this->getRequest()->getParam('makeFavorite')) {
			//@todo Replace with retrieval of actual user
		
			if($item->isFavoriteOf($user)) {
				//Make un-favorite
				$if = Doctrine_Manager::getInstance()->getTable('ItemsFavorites')->findBySql("user_id = {$user->id} AND item_id = {$item->id}");
				$if->delete();
			} else {
				//Make it favorite
				$if = new ItemsFavorites();
				$if->Item = $item;
				$if->User = $user;
				$if->save();
			}
		}
		if(!empty($_POST['tags'])) $this->addTags($item, $user);
		
		$item->refresh();
		
		echo $this->render('items/show.php', compact("item", 'user'));
	}
	
	/**
	 * @param Item
	 * @return void
	 **/
	private function addTags($item, $user) {
		
		if($tagString = $_POST['tags']) {
			$item->addTagString($tagString, $user);			
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
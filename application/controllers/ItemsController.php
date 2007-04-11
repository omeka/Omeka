<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
/**
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
		$query->select('i.*, t.*')->from('Item i');
		$query->leftJoin('Item.Tags t');
		$query->leftJoin('Item.Collection c');
		$query->leftJoin('i.Type ty');
				
		//replace with permissions check
		if(!$this->getRequest()->getParam('admin')) {
//			$query->where('items.public = 1');
		} 
		
		//filter based on featured
		if($featured = $this->_getParam('featured')) {
			$query->addWhere('i.featured = '.($featured == 'true' ? '1':'0'));
		}
		
		//filter based on collection
		if($collection = $this->_getParam('collection')) {
			if(is_numeric($collection)) {
				$query->addWhere('c.id = :collection', array('collection'=> $collection));
			}else {
				$query->addWhere('c.name = :collection', array('collection'=>$collection));
			}
		}
		
		//filter based on type
		if($type = $this->_getParam('type')) {
			if(is_numeric($type)) {
				$query->addWhere('ty.id = :type', compact('type'));
			}else {
				$query->addWhere('ty.name = :type', compact('type'));
			}
		}
		
		//filter based on tags
		if( ($tag = $this->_getParam('tag')) || ($tag = $this->_getParam('tags')) ) {
			
			if(!is_array($tag) )
			{
				$tag = explode(',', $tag);
			}
			foreach ($tag as $key => $t) {
				$key = 'tag'.$key;
				$query->addWhere("t.name = :$key", array($key=>$t));
			}			
		}
		
		//exclude Items with given tags
		if(($excludeTags = $this->_getParam('withoutTags'))) {
				//we are looking for Items that are tagged but not with a specific one(s)
				if(!is_array($excludeTags))
				{
					$excludeTags = explode(',', $excludeTags);
				}
				$where = array();
				foreach ($excludeTags as $key => $tag) {
					$key = 'noTag'.$key;
					$where[$key] = "t.name LIKE :$key";
					$params[$key] = $tag;
				}	
				$query->addWhere(
					"i.id NOT IN (SELECT i.id FROM Item i INNER JOIN i.ItemsTags it".
					" INNER JOIN it.Tag t WHERE ".join(' OR ',$where).")", $params );
		}
		
//		echo $query->getQuery();

		if(($from_record = $this->_getParam('relatedTo')) && @$from_record->exists()) {
			$componentName = $from_record->getTable()->getComponentName();
			$alias = $this->_table->getAlias($componentName);
			$query->innerJoin("Item.$alias rec");
			$query->addWhere('rec.id = ?', array($from_record->id));
		}
		
		if($recent = $this->_getParam('recent')) {
			$query->orderBy('i.added desc');
		}
		
		return $this->_browse->browse();
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
			$clean = $_POST;
			unset($clean['id']);
			
			if(!empty($clean['tags'])) {
				// @todo Replace with retrieval of actual logged in user
				$user = Doctrine_Manager::getInstance()->getTable('User')->find(1);
				$item->addTagString($clean['tags'], $user);
			}
			
			$item->setFromForm($clean);
					
			if(!empty($clean['change_type'])) return false;
			
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
			
			if($clean['public']) $item->public = 1;
			
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
	
		$tagsAdded = $this->commitForm($item);
		
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
		
		if($deleteTag = $this->_getParam('deleteTag'))
		{
			//@todo PERMISSIONS CHECK
			$isMyTag = $this->_getParam('isMyTag');
			$tagToDelete = $this->getTable('Tag')->find($deleteTag);
			
			if($tagToDelete) {
				if($isMyTag) {
					
					//delete that association
					$it = $this->getTable('ItemsTags')->findBySql('item_id = ? AND tag_id = ? AND user_id = ?',array($item->id,$deleteMyTag, $user->id))->getFirst();
					if($it) {
						$it->delete();
					}				

					if($tagToDelete->tagCount() == 0)
					{
						$tagToDelete->delete();
					}
				
				}else {
					$tagToDelete->delete();
				}
			}
			
			$tagsDeleted = true;
			
		}
		
		if($tagsAdded || $tagsDeleted) {
			//This is a workaround for the fact that the Tags collection doesn't get automatically refreshed
			$item->Tags = $this->getTable('Tag')->findSome(array('item_id'=>$item->id));
		}
		
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
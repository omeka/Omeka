<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Tag.php';
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class TagsController extends Kea_Controller_Action
{	
	public function init()
	{
		$this->_table = Doctrine_Manager::getInstance()->getTable('Tag');
		$this->_modelClass = 'Tag';
		
		$this->_joinTables = array('ExhibitsTags', 'ItemsTags');
	}
	
	public function editAction()
	{
		if($user = Kea::loggedIn()) {
			
			if(!empty($_POST)) {				
				$this->editTags($user);
			}
			
			//Having 'rename' permissions really means that user can rename everyone's tags
			if($this->isAllowed('rename')) {
				$tags = $this->getTagListWithCount();
			}else {
				$tags = $this->getTagListWithCount($user->id);
			}
			
			
			
			return $this->render('tags/edit.php', compact('tags'));
		}
	}
	
	public function deleteAction()
	{
		if($user = Kea::loggedIn()) {
			if(!empty($_POST)) {
				$tag_id = $_POST['delete_tag'];
				$tag = $this->_table->find($tag_id);
				if($this->isAllowed('remove')) {
					$tag->delete();
				}else {
					$tag->delete($user->id);
				}
				$this->flash("Tag named '{$tag->name}' was successfully deleted.");
			}
			if($this->isAllowed('remove')) {
				$tags = $this->getTagListWithCount();
			}else {
				$tags = $this->getTagListWithCount($user->id);
			}
			
			
			return $this->render('tags/delete.php', compact('tags'));
		}
	}
	
	protected function getTagListWithCount($user_id=null) {
			$select = new Kea_Select($this->getConn());
			$select->from('tags t', 't.*, (COUNT(et.id) + COUNT(it.id)) AS tagCount')
					->joinLeft('items_tags it', 'it.tag_id = t.id')
					->joinLeft('exhibits_tags et', 'et.tag_id = t.id')
					->group('t.id')
					->having('tagCount > 0');
			
			
			if($user_id) {
				//This user can only edit their own tags
				$select->where('it.user_id = ? OR et.user_id = ?', $user_id);
				$tags = $select->execute()->fetchAll();
			}else {
				//This user can edit everyone's tags
				$tags = $select->execute()->fetchAll();
			}
			
			return $tags;		
	} 
	
	protected function editTags($user)
	{
		$oldTagId = $_POST['old_tag'];
		
		//Explode and sanitize the new tags
		$newTags = explode(',', $_POST['new_tag']);
		foreach ($newTags as $k=>$t) {
			$newTags[$k] = trim($t);
		}
		$newTags = array_diff($newTags,array(''));
		
		$oldTag = $this->_table->find($oldTagId);
		
		$oldName = $oldTag->name;
		$newNames = $_POST['new_tag'];
		
		if($this->isAllowed('edit')) {
			$oldTag->rename($newTags);
		}
		else {
			$oldTag->rename($newTags, $user->id);
		}
		
		$this->flash("Tag named '$oldName' was successfully renamed to '$newNames'.");
	}
	
	/**
	 *
	 * @return void
	 **/
	public function browseAction()
	{
		$params = $this->_getAllParams();
		$perms = array();
		
		//Check to see whether it will be tags for exhibits or for items
		//Default is Item
		if(isset($params['tagType'])) {
			$for = $params['tagType'];
			unset($params['tagType']);
		}else {
			$for = 'Item';
		}
		//Since tagType must correspond to a valid classname, this will barf an error on Injection attempts
		if(!class_exists($for)) {
			throw new Exception( 'Invalid tagType given' );
		}else {
			$classFor = new $for;
		}
		
		
		if( ($for == 'Item') and !$this->isAllowed('showNotPublic','Items') ) {
			$perms['onlyPublic'] = true;
		}
		
		$tags = $this->_table->findSome(array_merge($params, $perms), $for);

		$total_results = count($tags);
		
		
		
		//Retrieve the total number of tags for 
		$joinTable = $classFor->getTagJoinTableName();
		$sql = "SELECT COUNT(t.id) FROM tags t INNER JOIN $joinTable j ON j.tag_id = t.id";
		
		if(!empty($perms['onlyPublic'])) {
			$sql .= " INNER JOIN ".$classFor->getTableName()." i ON i.id = j.item_id WHERE i.public = 1";
		}
		$total_tags = $this->getConn()->fetchOne($sql);
		
		Zend::register('total_tags', $total_tags);
		Zend::register('total_results', $total_results);	
		return $this->render('tags/browse.php',compact('tags','total_tags'));
	}
}
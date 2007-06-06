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
			
		/*
				$sql = "SELECT t.*, (COUNT(et.id) + COUNT(it.id)) AS tagCount
					FROM tags t
					LEFT JOIN items_tags it ON it.tag_id = t.id
					LEFT JOIN exhibits_tags et ON et.tag_id = t.id	
					GROUP BY t.id";
		*/	
			
			$select = new Kea_Select($this->getConn());
			$select->from('tags t', 't.*, (COUNT(et.id) + COUNT(it.id)) AS tagCount')
					->joinLeft('items_tags it', 'it.tag_id = t.id')
					->joinLeft('exhibits_tags et', 'et.tag_id = t.id')
					->group('t.id')
					->having('tagCount > 0');
			
			//Having 'rename' permissions really means that user can rename everyone's tags
			if(!$this->isAllowed('rename')) {
				$user_id = $user->id;
				//This user can only edit their own tags
				$select->where('it.user_id = ? OR et.user_id = ?', $user_id);
				$tags = $select->execute()->fetchAll();
			}else {
				//This user can edit everyone's tags
				$tags = $select->execute()->fetchAll();
			}
			
			return $this->render('tags/edit.php', compact('tags'));
		}
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
		
		if($this->isAllowed('edit')) {
			$oldTag->rename($newTags);
		}
		else {
			$oldTag->rename($newTags, $user->id);
		}
	}
	
	public function deleteAction()
	{
		
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
		
		
		if(!$this->isAllowed('showNotPublic','Items')) {
			$perms['onlyPublic'] = true;
		}
		
		$tags = $this->_table->findSome(array_merge($params, $perms), $for);

		$total_results = count($tags);
		
		//Retrieve the total number of tags for 
/*		$joinTable = $classFor->getTagJoinTableName();
		$sql = "SELECT COUNT(*) FROM tags t INNER JOIN $joinTable j ON j.tag_id = t.id";
		
		if(!empty($perms['onlyPublic'])) {
			$sql .= "INNER JOIN ".$classFor->getTableName()." i ON i.id = it.item_id WHERE i.public = 1";
		}
		$total_tags = $this->getConn()->fetchOne($sql);
*/			
		return $this->render('tags/browse.php',compact('tags','total_results', 'total_tags'));
	}
}
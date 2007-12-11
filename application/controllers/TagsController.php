<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Tag.php';
/**
 * @package Omeka
 **/
require_once 'Omeka/Controller/Action.php';
class TagsController extends Omeka_Controller_Action
{	
	public function init()
	{
		$this->_modelClass = 'Tag';	
	}
	
	public function editAction()
	{
		if($user = Omeka::loggedIn()) {
			
			if(!empty($_POST)) {				
				$this->editTags($user);
			}
					
			$tags = $this->getTagsforAdministration();
						
			return $this->render('tags/edit.php', compact('tags'));
		}
	}
	
	public function deleteAction()
	{
		if($user = Omeka::loggedIn()) {
			if(!empty($_POST)) {
				$tag_id = $_POST['delete_tag'];
				$tag = $this->_table->find($tag_id);
				if($this->isAllowed('remove')) {
					$tag->delete();
				}else {
					$tag->deleteForEntity($user->Entity);
				}
				$this->flashSuccess("Tag named '{$tag->name}' was successfully deleted.");
			}
						
			$tags = $this->getTagsForAdministration();
			
			return $this->render('tags/delete.php', compact('tags'));
		}
	}
	
	protected function getTagsForAdministration()
	{
		$user = Omeka::loggedIn();
		
		if(!$user) {
			throw new Exception( 'You have to be logged in to edit tags!' );
		}
		
		$criteria = array('sort'=>'most');
		
		//Having 'rename' permissions really means that user can rename everyone's tags
		if(!$this->isAllowed('rename')) {
			$criteria['user'] = $user->id;
		}
		
		$tags = $this->_table->findBy($criteria);
		
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
		}
		
		if($record = $this->_getParam('record')) {
			$filter['record'] = $record;
		}
		
		//For the count, we only need to check based on permission levels
		$count_params = array_merge($perms, array('return'=>'count', 'limit'=>false, 'recent'=>false));
		
		$total_tags = $this->_table->findBy($count_params, $for, true);

		
		$tags = $this->_table->findBy(array_merge($params, $perms), $for);

		$total_results = count($tags);

		Zend_Registry::set('total_tags', $total_tags);

		Zend_Registry::set('total_results', $total_results);	
		
		//Plugin hook
		fire_plugin_hook('browse_tags',  $tags, $for);
		
		$browse_for = $for;
		
		return $this->render('tags/browse.php',compact('tags','total_tags', 'browse_for'));
	}
}
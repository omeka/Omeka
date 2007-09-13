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
		$this->_table = $this->getTable('Tag');
		$this->_modelClass = 'Tag';	
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
					$tag->delete($user->Entity);
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
		
		if($record = $this->_getParam('record')) {
			$filter['record'] = $record;
		}
		
		
		if( ($for == 'Item') and !$this->isAllowed('showNotPublic','Items') ) {
			$perms['public'] = true;
		}
		
		$total_tags = $this->_table->findBy($perms, $for, true);

		$tags = $this->_table->findBy(array_merge($params, $perms), $for);

		$total_results = count($tags);

		Zend::register('total_tags', $total_tags);
		Zend::register('total_results', $total_results);	
		
		//Plugin hook
		fire_plugin_hook('browse_tags',  $tags, $for);
		
		return $this->render('tags/browse.php',compact('tags','total_tags'));
	}
}
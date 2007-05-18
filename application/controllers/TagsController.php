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
	}
	
	/**
	 * @todo All permissions checks for tags should go here
	 *
	 * @return void
	 **/
	public function browseAction()
	{
		$params = $this->_getAllParams();
		$perms = array();
		
		if(!$this->isAllowed('showNotPublic','Items')) {
			$perms['onlyPublic'] = true;
		}
		
		$tags = $this->_table->findAll(array_merge($params, $perms));

		$total_results = count($tags);
		
		//Retrieve the total number of tags for 
		$sql = "SELECT COUNT(*) FROM tags t";
		if(!empty($perms['onlyPublic'])) {
			$sql .= ' INNER JOIN items_tags it ON it.tag_id = t.id INNER JOIN items i ON i.id = it.item_id WHERE i.public = 1';
		}
		$total_tags = $this->getConn()->fetchOne($sql);
			
		return $this->render('tags/browse.php',compact('tags','total_results', 'total_tags'));
	}
}
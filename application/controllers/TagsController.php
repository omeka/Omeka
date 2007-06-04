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
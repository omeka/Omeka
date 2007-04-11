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
		
		if(!$this->_getParam('admin')) {
			$params['onlyPublic'] = true;
		}
		
		$tags = $this->_table->findAll($params);

		$total = count($tags);
		return $this->render('tags/browse.php',compact('tags','total'));
	}
}
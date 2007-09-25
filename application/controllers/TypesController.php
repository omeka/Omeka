<?php
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Metafield.php';
/**
 * @package Omeka
 **/
require_once 'Omeka/Controller/Action.php';
class TypesController extends Omeka_Controller_Action
{
	public function init()
	{
		$this->_modelClass = 'Type';
		$this->_table = $this->getTable('Type');
	}

	protected function loadFormData() 
	{
		$id = $this->getRequest()->getParam('id');
		$type = $this->getTable('Type')->find($id);
		$metafields = $this->getTable('Metafield')->findMetafieldsWithoutType($type);
		$this->_view->assign(compact('metafields'));
	}
	
	public function metafieldsAction()
	{
		return $this->getTable('Metafield')->findAll();
	}
}
?>
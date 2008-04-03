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
	}

	public function metafieldsAction()
	{
		return $this->getTable('Metafield')->findAll();
	}
}
?>
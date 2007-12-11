<?php
/**
 * @package Omeka
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Collection.php';
require_once 'Omeka/Controller/Action.php';
class CollectionsController extends Omeka_Controller_Action
{
	public function init()
	{
		$this->_modelClass = 'Collection';
	}
}
?>
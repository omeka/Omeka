<?php
/**
 * @package Omeka
 * @author Nate Agrin
 **/
require_once 'Kea/Controller/Action.php';
class TypesController extends Kea_Controller_Action
{
	public function init()
	{
		$this->_modelClass = 'Type';
		$this->_table = Doctrine_Manager::getInstance()->getTable('Type');
	}
	
}
?>
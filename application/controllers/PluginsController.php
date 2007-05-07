<?php
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class PluginsController extends Kea_Controller_Action
{
	public function init()
	{
		$this->_modelClass = 'Plugin';
		$this->_table = Doctrine_Manager::getInstance()->getTable('Plugin');
	}
	
	public function browseAction() {
		Doctrine_Manager::connection()->getTable('Plugin')->installNew();
		return parent::browseAction();
	}

	/**
	 * save the form values to the db
	 *
	 * @return boolean
	 **/
	protected function commitForm($plugin)
	{	
		if(empty($_POST)) return false;

		$plugin->config = $_POST['config'];
		
		if($_POST['active']) {
			$plugin->active = (int) !($plugin->active);
		}
		try{
			$plugin->save();
			return true;
		}catch( Exception $e) {
			return false;
		}

	}
	
	public function deleteAction() {$this->_redirect('/');}
	
	public function addAction() {$this->_redirect('/');}
}
?>
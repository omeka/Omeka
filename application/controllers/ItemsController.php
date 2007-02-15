<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
/**
 * @package Omeka
 * @author Nate Agrin, Kris Kelly
 **/
require_once 'Kea/Controller/Action.php';
class ItemsController extends Kea_Controller_Action
{	
	protected $_protected = array('browse', 'delete');
	
	public function init() 
	{
		$this->_table = Doctrine_Manager::getInstance()->getTable('Item');
		$this->_modelClass = 'Item';
		$this->_browse = new Kea_Controller_Browse_Paginate('Item', $this);
	}

	protected function commitForm($item)
	{
		//add code here to handle anything aside from copying the form directly to the item
		return parent::commitForm($item);
	}
	
	public function showAction() 
	{
		$item = $this->findById();
		if(!empty($_POST['tags'])) $this->addTags($item);
		echo $this->render('items/show.php', compact("item"));
	}

	private function addTags($item) {
		/* TODO: catch the current User somewhere in here, shoot it to the addTagString() method */
		if($tagString = $_POST['tags']) {
			$item->addTagString($tagString);			
			try{
				$item->save();
				$item->refresh();
			//This error processing part should be abstracted out to the individual models, at least if all we want to do is display the errors
			}catch(Doctrine_Validator_Exception $e) {
				$item->gatherErrors($e);
			}
		}
	}
}
?>
<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
/**
 * @package Omeka
 * @author Nate Agrin, Kris Kelly
 **/
require_once 'Kea/Controller/Action.php';
class ItemsController extends Kea_Controller_Action
{	
	protected $_protected = array('browse');

    public function indexAction()
    {
		$this->browseAction();
    }

	public function browseAction()
	{
		//Should be mutable, possibly a POST variable with a default stored in a config file
		$per_page = 12;
		
		$page = $this->getRequest()->getParam('page');
		if(!$page) $page = 1;
		
		$offset = ($page - 1) * $per_page;
		
		$table = Doctrine_Manager::getInstance()->getTable('Item');
		
		$items = $table->createQuery()
				   		->limit($per_page)
				   		->offset($offset)
				   		->execute();
		
		$total = $table->count();
				
		$this->render("items/browse.php", compact("total", "offset", "items"));
	}

    public function noRouteAction()
    {
        $this->_redirect('/');
    }

	public function showAction() 
	{
		$this->view->item = $this->find();
		if(!empty($_POST['tags'])) $this->addTags($this->view->item);
		echo $this->view->render('show.php');
	}
	
	//This should essentially be built into controllers as well, functional equivalent to findById() in the old system
	protected function find() {
		$id = $this->getRequest()->getParam('id');
		$item = Doctrine_Manager::getInstance()->getTable('Item')->find($id);
		return $item;
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
				$invalid = $e->getInvalidRecords();
				foreach( $invalid as $record )
				{
					foreach( $record->getErrorStack() as $key => $errorArray )
					{
						foreach( $errorArray as $errorKey => $error )
						{
							switch($error) {
								case 'duplicate':
									echo 'Error: Tag has already been added to this item by this user.';
								break;
							}
						}
					}
				}
			}
		}
	}
}
?>
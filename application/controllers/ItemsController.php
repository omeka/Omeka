<?php

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';
/**
 * @package Sitebuilder
 * @author Nate Agrin
 **/
require_once 'Zend/Controller/Action.php';
class ItemsController extends Zend_Controller_Action
{
	//Duplicated in other controllers (should be abstracted by the layout/theme system)
	public function init() {
		$view = new Kea_View;
		$this->view_path = PUBLIC_DIR.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'items';
		$view->setScriptPath($this->view_path);
		$this->view = $view;		
	}
	
	
    public function indexAction()
    {
		$this->_forward('items', 'browse');
    }

	public function browseAction()
	{
		$this->getResponse()->appendBody('foo');
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
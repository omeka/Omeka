<?php
/**
 * @package Sitebuilder
 * @author Nate Agrin
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'File.php';

require_once 'Zend/Controller/Action.php';
class FilesController extends Zend_Controller_Action
{
	public function init() {
		$view = new Kea_View;
		$this->view_path = PUBLIC_DIR.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'files';
		$view->setScriptPath($this->view_path);
		$this->view = $view;		
	}
	
    public function indexAction()
    {
		echo 'This is the '.get_class($this);
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }

	public function addAction() 
	{
		if(!empty($_POST)) {
			
			foreach( $_FILES['itemfile']['error'] as $key => $error )
			{
				try{
					$file = new File();
					$file->upload('itemfile', $key);
					$file->save();	
				} catch(Exception $e) {
					$file->delete();
					var_dump( $e );exit;
				}
			}
		}
		echo $this->view->render('add.php');
	}
}
?>
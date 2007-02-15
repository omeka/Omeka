<?php
/**
 * @package Omeka
 * @author Nate Agrin
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'File.php';

require_once 'Kea/Controller/Action.php';
class FilesController extends Kea_Controller_Action
{
	public function init() {
		$this->_modelClass = 'File';
		$this->_table = Doctrine_Manager::getInstance()->getTable('File');		
	}
	
	/**
	 * This will probably relocate to the commitForm method of ItemsController, seeing as files cannot exist without items
	 *
	 * @return void
	 **/
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
		$this->render('files/add.php');
	}
	
	public function indexAction() { $this->_redirect('items/browse/'); }
	
	// Should not browse files by themselves
	public function browseAction() { $this->indexAction(); }
}
?>
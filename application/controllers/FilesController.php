<?php
/**
 * @package Omeka
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'File.php';

require_once 'Kea/Controller/Action.php';
class FilesController extends Kea_Controller_Action
{
	public function init() {
		$this->_modelClass = 'File';
		$this->_table = Doctrine_Manager::getInstance()->getTable('File');		
	}
	
	public function indexAction() { $this->_redirect('/'); }
	
	// Should not browse files by themselves
	public function browseAction() { $this->indexAction(); }
	
	public function addAction() {$this->indexAction();}
	
	protected function commitForm($file) {
		$immutable = array(
			'id', 
			'modified', 
			'added', 
			'authentication', 
			'thumbnail_filename', 
			'archive_filename', 
			'fullsize_filename', 
			'original_filename', 
			'mime_browser', 
			'mime_php', 
			'mime_os', 
			'type_os');
		foreach ($immutable as $value) {
			unset($_POST[$value]);
		}
		return parent::commitForm($file);
	}
	
	public function showAction()
	{
		$file = $this->findById();
						
		if(!$file->isPublic() && !$this->isAllowed('showNotPublic','Items')) {
			$this->_redirect('403');
		}
		
		Zend::Register('file', $file);
		$this->render('files/show.php',compact('file'));
	}
}
?>
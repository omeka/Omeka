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
		$this->_table = $this->getTable('File');		
	}
	
	public function indexAction() { $this->_redirect('/'); }
	
	// Should not browse files by themselves
	public function browseAction() { $this->indexAction(); }
	
	public function addAction() {$this->indexAction();}
	
	public function showAction()
	{
		$file = $this->findById();
						
		$this->checkFilePermission($file);				
								
		Zend::register('file', $file);
		$this->render('files/show.php',compact('file'));
	}
	
	protected function checkFilePermission($file)
	{
		if(!$file->isPublic() && !$this->isAllowed('showNotPublic','Items')) {
			$this->forbiddenAction();
		}		
	}

	protected function isValidFormat($format)
	{
		return in_array($format, array('fullsize','thumbnail','archive'));
	}

	protected function isValidDisposition($type)
	{
		return in_array($type, array('attachment', 'inline'));
	}

	public function getAction()
	{
		$format = $this->_getParam('format');
		
		if(!$this->isValidFormat($format)) {
			$this->forbiddenAction();
		}
		
		$file = $this->findById();
		
		$this->checkFilePermission($file);
		
		
		//If we don't have any images associated with this file, then use the full archive path
		if(!$file->has_derivative_image) {
			$format = 'archive';
		}

		//Otherwise use the chosen format of the image
		
		$path = $file->getPath($format);

		header('Content-type: ' . (string) $file->mime_browser);

		$type = $this->_getParam('type');
		
		if(!$this->isValidDisposition($type)) {
			$this->forbiddenAction();
		}

		// It will be called downloaded.pdf
		header('Content-Disposition: ' . $type . '; filename="' . $file->original_filename . '"');

		// The PDF source is in original.pdf
		readfile($path);
	}
}
?>
<?php
/**
 * @package Omeka
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'File.php';

require_once 'Omeka/Controller/Action.php';
class FilesController extends Omeka_Controller_Action
{
	public function init() {
		$this->_modelClass = 'File';
	}
	
	public function indexAction() { $this->_redirect('/'); }
	
	// Should not browse files by themselves
	public function browseAction() { $this->indexAction(); }
	
	public function addAction() {$this->indexAction();}
	
	public function showAction()
	{
		$file = $this->findById();
														
		Zend_Registry::set('file', $file);
		$this->render('files/show.php',compact('file'));
	}

	protected function isValidFormat($format)
	{
		return in_array($format, array('fullsize','thumbnail','archive','square_thumbnail'));
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
		
		//If we don't have any images associated with this file, then use the full archive path
		if(!$file->has_derivative_image) {
			$format = 'archive';
		}

		//Otherwise use the chosen format of the image
		
		$path = $file->getWebPath($format);

		header('Location: '.$path);
	}
}
?>
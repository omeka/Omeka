<?php 
/**
* 
*/
class ErrorController extends Omeka_Controller_Action
{
	public function errorAction()
	{
		$debug = Zend_Registry::get('config_ini')->debug->exceptions;
		
		$handler = $this->_getParam('error_handler');
		
		$e = $handler->exception;
		
		switch ($handler->type) {
			//Errors that involve missing controller/action may be requests for static pages
			case 'EXCEPTION_NO_CONTROLLER':
			case 'EXCEPTION_NO_ACTION':
				try {
					return $this->renderStaticPage($handler->request);
				} catch (Exception $e) {}
				break;
			default:
				//Log errors that aren't just for pages that don't exist
				Omeka_Logger::logError( $e );
				break;
		}		
				
		if($debug) {
			include CORE_DIR . DIRECTORY_SEPARATOR .'404.php';
			return;
		}	
		else {
			return $this->render('404.php');
		}
	}
	
	protected function renderStaticPage($req)
	{
		$c = $req->getControllerName();
		$a = $req->getActionName();
		
		//'index' action corresponds to a uri like foobar/
		if($a == 'index') {
			$page = $c;
			$dir = null;
		}
		//Any combo of controller/action corresponds to a page like foobar/thing.php
		else {
			$page = $a;
			$dir = $c;
		}
		
		if(!$dir) {
			$file = $page . '.php';
		}else {
			$file = $dir . DIRECTORY_SEPARATOR . $page . '.php';
		}
		
		return $this->render($file);
	}
}
 
?>

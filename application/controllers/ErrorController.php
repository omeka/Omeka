<?php 
/**
* @todo In future, all errors in other controllers should throw exceptions, which cause the request to go here.
* There should be different pages for different kinds of errors.  Not just 404.
* Also find a way to respond to errors in requests for data feeds.
*	Non-existent feed should route here (to XHTML output)
*	Other errors should probably be handled by the Omeka_View_Format_Abstract implementations
*/
class ErrorController extends Omeka_Controller_Action
{
	public function errorAction()
	{
		//Are we in debugging mode?
		$debug = Zend_Registry::get('config_ini')->debug->exceptions;
		
		$handler = $this->_getParam('error_handler');
		
		//The exception that barfed (may need to handle this differently in future)
		$e = $handler->exception;
		
		//Make sure we try to output the error pages as valid XHTML (if an invalid format was chosen)
		if($e instanceof Omeka_View_Format_Invalid_Exception) {
			$this->getRequest()->setParam('output', 'xhtml');
		}		
		
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
			//The built-in global error page, used only when debugging (provides a dump of the exception)
			include CORE_DIR . DIRECTORY_SEPARATOR .'404.php';
			return;
		}	
		else {
			//Try to render the theme's 404 page.  If the 404 page doesn't exist, no idea what happens
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

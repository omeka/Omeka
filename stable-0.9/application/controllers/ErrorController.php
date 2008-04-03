<?php 
/**
* @todo In future, all errors in other controllers should throw exceptions, which cause the request to go here.
* Also find a way to respond to errors in requests for data feeds.
*	Non-existent feed should route here (to XHTML output)
*	Other errors should probably be handled by the Omeka_View_Format_Abstract implementations
*/
class ErrorController extends Omeka_Controller_Action
{
	public function errorAction()
	{
		//This is the pattern for retrieving the exception that occurred
		$handler = $this->_getParam('error_handler');		
		$e = $handler->exception;
		
		//Make sure we try to output the error pages as valid XHTML (if an invalid format was chosen)
		if($e instanceof Omeka_View_Format_Invalid_Exception) {
			$this->getRequest()->setParam('output', 'xhtml');
		}		
		
		if($this->is404($e)) {
		    return $this->render404($e);
		}
		
		if($this->is403($e)) {
		    return $this->render403($e);
		}
		
		//Try to determine what kind of error occurred
		switch ($handler->type) {
			
			//Errors that involve missing controller/action may be requests for static pages
			case 'EXCEPTION_NO_CONTROLLER':
			case 'EXCEPTION_NO_ACTION':
				
				try {
					return $this->renderStaticPage($handler->request);
				} catch (Exception $e) {
				    //If there is an exception thrown from this, it means render the 404 page
				    return $this->render404();
				}
				break;
			default:
				break;
		}		
		
		//Log errors that aren't just for pages that don't exist
		Omeka_Logger::logError( $e );
				
        return $this->renderException($e);
	}
	
	/**
	 * Check to see whether the error qualifies as a 404 error
	 *
	 * @return boolean
	 **/
	protected function is404(Exception $e)
	{
	    //Controller Exceptions should render a 404 page
        if($e instanceof Omeka_Controller_Exception_404)
		{
		    return true;
		}	    
		
		//@hack - If it's a 'script not found' error, its a 404
		if($e instanceof Zend_View_Exception)
		{
		    $msg = $e->getMessage();
		    
		    return (bool) preg_match('/script.*not found in path/', $msg);
		}
	}
	
	protected function is403(Exception $e)
	{
	    return ($e instanceof Omeka_Controller_Exception_403);
	}
	
	protected function renderException(Exception $e)
	{
//	    $this->_view->setScriptPath(CORE_DIR . DIRECTORY_SEPARATOR . 'templates');
	    
	    if($this->isInDebugMode()) {
	        ini_set('memory_limit', '64M');
//	        Zend_Debug::dump( $this->_view );exit;
	        return $this->renderCoreTemplate('errors/debug.php', compact('e'));
	    }
	    else {
	        return $this->renderCoreTemplate('errors/index.php', compact('e'));
	    }
	}
	
	/**
	 * We need to have this workaround for the Error Controller to be able to render pages that are built in to the app
	 *
	 * @duplication UpgradeController::init() and UpgradeController::render()
	 * @return void
	 **/
	protected function renderCoreTemplate($file, $vars = array())
	{
        $this->_view = new Omeka_View($this);
		$this->_view->addScriptPath(CORE_DIR . DIRECTORY_SEPARATOR . 'templates');
		$this->_view->addAssetPath(
		    CORE_DIR . DIRECTORY_SEPARATOR . 'templates', 
		    WEB_ROOT . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'templates');
		
		require_once HELPERS;

	    $this->_view->assign($vars);
	    $body = $this->_view->render($file);
	    $this->getResponse()->appendBody($body);
	}
    
	protected function render404()
	{
	    $this->getResponse()->setHttpResponseCode(404);
	    
	    try {
	        return $this->render('404.php');
	    } catch (Exception $e) {
	        $badUri = $this->getRequest()->getRequestUri();
	        return $this->renderCoreTemplate('errors/404.php', compact('badUri'));
	    }
	}
	
	protected function render403()
	{
	    $this->getResponse()->setHttpResponseCode(403);
	    
	    try {
		    return $this->render('403.php');
	    } catch (Exception $e) {
	       return $this->renderCoreTemplate('errors/403.php');
	    } 
	}
	
	protected function isInDebugMode()
	{
	    return (bool) Zend_Registry::get('config_ini')->debug->exceptions;
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

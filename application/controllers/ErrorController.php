<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Handles all exceptions that are thrown in controllers.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ErrorController extends Omeka_Controller_Action
{
    public function errorAction()
    {
        // Drop down to built-in error views if and only if we are in debug mode
        // These are the default script paths that need to be known to the app
        // @internal does setAssetPath() need to have this same value in 
        // Omeka_View::__construct()?
        if ($this->isInDebugMode()) {            
            $this->view->setScriptPath(VIEW_SCRIPTS_DIR);
            $this->view->setAssetPath(VIEW_SCRIPTS_DIR, WEB_VIEW_SCRIPTS);
        }
        
        $handler = $this->_getParam('error_handler');        
        $e = $handler->exception;
        
        if ($this->is404($e, $handler)) {
            return $this->_forward('not-found');
        }
        
        if ($this->is403($e)) {
            return $this->_forward('forbidden');
        }
        
        $this->logException($e, Zend_Log::ERR);
        
        return $this->renderException($e);
    }
    
    protected function _getException()
    {
        $handler = $this->_getParam('error_handler');    
        if ($handler) {
            return $handler->exception;
        }
    }
    
    /**
     * Generic action to render a 404 page.
     * 
     * @param string
     * @return void
     */
    public function notFoundAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
        if (!($e = $this->_getException())) {
            $e = new Exception(__("Page not found."));
        }
        $this->view->assign(array('badUri' => $this->getRequest()->getRequestUri(), 
                                  'e' => $e));
        
        // Render the error script that displays debugging info.
        if ($this->isInDebugMode()) {
            $this->view->displayError = true;
            $this->render('index');
        } else {
            $this->render('404');
        }
    }
    
    public function forbiddenAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
        // Fake an exception if there isn't one in the request.
        if (!($e = $this->_getException())) {
            $e = new Omeka_Controller_Exception_403(__("Access denied."));
        }
        $this->view->assign(array('e' => $e));
        
        // Render the error script that displays debugging info.
        if ($this->isInDebugMode()) {
            $this->view->displayError = true;
            $this->render('index');
        } else {
            $this->render('403');
        }
    }

    public function methodNotAllowedAction()
    {
        $this->getResponse()->setHttpResponseCode(405);
        $this->view->method = $this->getRequest()->getMethod();
        $this->render('405');
    }
    
    private function logException($e, $priority)
    {
        $logger = $this->getInvokeArg('bootstrap')->getResource('Logger');
        if ($logger) {
            $logger->log($e, $priority);
        }
    }
    
    /**
     * Check to see whether the error qualifies as a 404 error
     *
     * @return boolean
     */
    protected function is404(Exception $e, $handler)
    {
        return ($e instanceof Omeka_Controller_Exception_404 
                || $e instanceof Zend_View_Exception 
                || $handler->type == 'EXCEPTION_NO_CONTROLLER' 
                || $handler->type == 'EXCEPTION_NO_ACTION');
    }
    
    protected function is403(Exception $e)
    {
        return ($e instanceof Omeka_Controller_Exception_403);
    }
    
    protected function renderException(Exception $e)
    {
        $this->view->e = $e;
        $this->view->displayError = $this->isInDebugMode();
        $this->render('index');
    }
    
    protected function isInDebugMode()
    {
        $config = $this->getInvokeArg('bootstrap')->getResource('Config');
        return (bool) $config->debug->exceptions;
    }
}

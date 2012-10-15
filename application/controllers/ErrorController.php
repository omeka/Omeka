<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class ErrorController extends Omeka_Controller_AbstractActionController
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

        $this->view->badUri = $this->getRequest()->getRequestUri();
        
        // Render the error script that displays debugging info.
        if ($this->isInDebugMode()) {
            if (!($e = $this->_getException())) {
                $e = new Omeka_Controller_Exception_404(__("Page not found."));
            }
            $this->renderException($e);
        } else {
            $this->render('404');
        }
    }
    
    public function forbiddenAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
        
        // Render the error script that displays debugging info.
        if ($this->isInDebugMode()) {
            if (!($e = $this->_getException())) {
                $e = new Omeka_Controller_Exception_403(__("Access denied."));
            }
            $this->renderException($e);
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
        $environment = $this->getInvokeArg('bootstrap')->getApplication()->getEnvironment();

        // Don't show error messages in production.
        $this->view->displayError = ($environment != 'production');
        $this->render('index');
    }
    
    protected function isInDebugMode()
    {
        $config = $this->getInvokeArg('bootstrap')->getResource('Config');
        return (bool) $config->debug->exceptions;
    }
}

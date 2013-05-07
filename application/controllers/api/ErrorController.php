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
    /**
     * 404 Not Found
     */
    const DEFAULT_HTTP_RESPONSE_CODE = 500;
    
    /**
     * Handle all API errors.
     */
    public function errorAction()
    {
        $exception = $this->_getParam('error_handler')->exception;
        
        $code = $exception->getCode();
        if (!$code) {
            $code = self::DEFAULT_HTTP_RESPONSE_CODE;
        }
        $message = $exception->getMessage();
        if (!$message) {
            $message = Zend_Http_Response::responseCodeAsText($code);
        }
        $message = array('message' => $message);
        
        try {
            $this->getResponse()->setHttpResponseCode($code);
        } catch (Zend_Controller_Exception $e) {
            // The response code was invalid. Set the default.
            $this->getResponse()->setHttpResponseCode(self::DEFAULT_HTTP_RESPONSE_CODE);
        }
        $this->_helper->json($message);
    }
}

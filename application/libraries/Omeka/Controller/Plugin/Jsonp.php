<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Sets the Content-Type header for all JSON-P requests.
 * 
 * @package Omeka\Controller\Plugin
 */
class Omeka_Controller_Plugin_Jsonp extends Zend_Controller_Plugin_Abstract
{
    /**
     * Callback parameter key.
     */
    const CALLBACK_KEY = 'callback';
    
    /**
     * Set the 'Content-Type' HTTP header to 'application/x-javascript' for
     * omeka-json requests.
     * 
     * @param Zend_Controller_Request_Abstract $request Request object.
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ('omeka-json' == $request->getParam('output') 
            && $request->getParam(self::CALLBACK_KEY)) {
            $this->getResponse()->setHeader('Content-Type', 'application/x-javascript');
        }
    }
}


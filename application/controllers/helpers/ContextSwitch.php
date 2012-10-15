<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Extends the default ContextSwitch action helper to enable JSONP.
 * 
 * @package Omeka\Controller\ActionHelper
 */
class Omeka_Controller_Action_Helper_ContextSwitch extends Zend_Controller_Action_Helper_ContextSwitch
{
    /**
     * This extends the default ZF JSON serialization to work with JSONP, which
     * enables cross-site AJAX using JSON.
     * 
     * @return void
     */
    public function postJsonContext()
    {
        parent::postJsonContext();

        if ($this->getAutoJsonSerialization() 
            and $callbackParam = $this->getRequest()->get(Omeka_Controller_Plugin_Jsonp::CALLBACK_KEY)) {
            $response = $this->getResponse();
            $json = $response->getBody();
            $response->setBody($callbackParam . '(' . $json . ')');
            
            // Also be sure to set the content header to 'text/javascript'.
            $response->setHeader('Content-Type', 'text/javascript');
        }
    }    
}

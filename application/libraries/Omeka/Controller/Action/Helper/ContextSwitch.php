<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Extends the default ContextSwitch action helper to enable JSONP.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
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

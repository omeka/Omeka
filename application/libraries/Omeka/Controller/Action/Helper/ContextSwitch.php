<?php 
class Omeka_Controller_Action_Helper_ContextSwitch extends Zend_Controller_Action_Helper_ContextSwitch
{
    
    /**
     * This extends the default ZF JSON serialization to work with JSONP, which
     * enables cross-site AJAX using JSON.
     * 
     * @return void
     **/
    public function postJsonContext()
    {
        parent::postJsonContext();

        if ($this->getAutoJsonSerialization() 
            and $callbackParam = $this->getRequest()->get('callback')) {
            $response = $this->getResponse();
            $json = $response->getBody();
            $response->setBody($callbackParam . '(' . $json . ')');
            
            // Also be sure to set the content header to 'text/javascript'.
            $response->setHeader('Content-Type', 'text/javascript');
        }
    }    
}

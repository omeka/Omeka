<?php 

/**
* 
*/
class Omeka_Controller_Action_Helper_ContextSwitch extends Zend_Controller_Action_Helper_ContextSwitch
{
    public function postJsonContext()
    {
        parent::postJsonContext();

        if ($this->getAutoJsonSerialization() 
            and $callbackParam = $this->getRequest()->get('callback')) {
            $response = $this->getResponse();
            $json = $response->getBody();
            $response->setBody($callbackParam . '(' . $json . ')');
        }
    }
}

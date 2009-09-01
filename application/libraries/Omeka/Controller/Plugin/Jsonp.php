<?php
class Omeka_Controller_Plugin_Jsonp extends Zend_Controller_Plugin_Abstract
{
    const CALLBACK_KEY = 'callback';
    
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ('omeka-json' == $request->getParam('output') 
            && $request->getParam(self::CALLBACK_KEY)) {
            $this->getResponse()->setHeader('Content-Type', 'application/x-javascript');
        }
    }
}
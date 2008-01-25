<?php 
/**
* 
*/
class Omeka_Controller_Plugin_Upgrader extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $options = Omeka_Context::getInstance()->getOptions();
        
        if($options['migration'] < OMEKA_MIGRATION) {
            $request->setControllerName('upgrade');
            $request->setActionName('migrate');            
        }
    }
}
 
?>

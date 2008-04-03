<?php 
/**
 * Check to see if controllers added by plugins are being called.
 *
 * This is a hack that is necessary because as of ZF 1.0, the front controller
 * can only associate a single directory with a single 'module', i.e. there can  
 * only be one directory in the default namespace.  Since controllers added by 
 * plugins need to be in the default namespace in order to avoid messy hacks,
 * this seems necessary (for now).
 *
 * @package Omeka
 * @author CHNM
 **/
class Omeka_Controller_Plugin_PluginControllerHack extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $dispatcher = $front->getDispatcher();
        $request = $this->getRequest();
        $pluginBroker = Zend_Registry::get('plugin_broker');
        
        $controllerDirs = $pluginBroker->getControllerDirs();
        
        //This loop will check through all the controller directories added by plugins,
        //provided that the current request is not dispatchable
        while( !($isDispatchable = $dispatcher->isDispatchable($request)) ) {
            $pluginDirectory = array_pop($controllerDirs);
            
            if($pluginDirectory) {
                $front->setControllerDirectory($pluginDirectory);
            }else {
                break;
            }
        }
        
        //If the request is still not dispatchable after all that nonsense
        //Then restore the controller directory to its original value
        //That way, it can find the ErrorController to handle the error
        if(!$isDispatchable) {
            $front->setControllerDirectory(CONTROLLER_DIR);
        }
    }
} 
?>

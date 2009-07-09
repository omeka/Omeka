<?php
class Omeka_Controller_Plugin_Installer extends Zend_Controller_Plugin_Abstract
{   
    /**
     * If Omeka has not been installed yet, make sure we dispatch to the
     *  notification in the InstallerController.
     * 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $request->setControllerName('installer');
        $request->setActionName('notify');  
    }
}
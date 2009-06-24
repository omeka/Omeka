<?php 

/**
 * Fire the 'initialize' hook for all plugins.  Note that
 * this hook fires before the front controller has been initialized or
 * dispatched.
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Plugins extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('PluginBroker');
        $broker = $bootstrap->getResource('PluginBroker');
        // Fire all the 'initialize' hooks for the plugins
        $broker->initialize();
        
    }
}

<?php 

/**
* 
*/
class Omeka_Core_Resource_PluginBroker extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        // Initialize the plugin broker with the database object and the 
        // plugins/ directory
        $broker = new Omeka_Plugin_Broker($this->getBootstrap()->getResource('db'), PLUGIN_DIR);   
        // $broker->loadActive();
        return $broker;
    }
}

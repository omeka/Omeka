<?php 

/**
* 
*/
class Omeka_Core_Resource_Pluginbroker extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Db');
        // Initialize the plugin broker with the database object and the 
        // plugins/ directory
        $broker = new Omeka_Plugin_Broker($bootstrap->getResource('Db'), PLUGIN_DIR);   
        $broker->loadActive();
        return $broker;
    }
}

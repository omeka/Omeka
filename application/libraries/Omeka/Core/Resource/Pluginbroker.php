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
        // Bootstrap the options as well, to make them immediately available
        // to plugins.
        $bootstrap->bootstrap('Options');
        // Initialize the plugin broker with the database object and the 
        // plugins/ directory
        $broker = new Omeka_Plugin_Broker($bootstrap->getResource('Db'), PLUGIN_DIR);   
        
        // Set the plugin broker before loading any plugins.  
        // This is important for helper globa functions like get_plugin_ini 
        // which use the plugin broker when plugins are loaded
        Omeka_Context::getInstance()->setPluginBroker($broker);
        
        $broker->loadActive();
        return $broker;
    }
}

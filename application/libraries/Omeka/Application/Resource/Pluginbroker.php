<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Set up the plugin broker.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Pluginbroker extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Omeka_Plugin_Broker
     */
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        $bootstrap->bootstrap('Db');
        // Bootstrap the options as well, to make them immediately available
        // to plugins.
        $bootstrap->bootstrap('Options');
        // Initialize the plugin broker with the database object and the 
        // plugins/ directory
        $db = $bootstrap->getResource('Db');
        $factory = new Omeka_Plugin_Broker_Factory(PLUGIN_DIR);
        $objs = $factory->getAll();
        $pluginLoader = $objs['plugin_loader'];
        $pluginLoader->loadPlugins($db->getTable('Plugin')->findAll());
        // Alias plugin loader for BC.
        Zend_Registry::set('pluginloader', $pluginLoader);
        return $objs['pluginbroker'];
    }
}

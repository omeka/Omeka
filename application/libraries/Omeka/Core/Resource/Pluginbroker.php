<?php 
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Set up the plugin broker.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Core_Resource_Pluginbroker extends Zend_Application_Resource_ResourceAbstract
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
        $broker = new Omeka_Plugin_Broker;   
        
        $pluginIniReader = new Omeka_Plugin_Ini(PLUGIN_DIR);
        $pluginMvc = new Omeka_Plugin_Mvc(PLUGIN_DIR);
        $pluginLoader = new Omeka_Plugin_Loader($broker, 
                                                $pluginIniReader,
                                                $pluginMvc,
                                                PLUGIN_DIR);
        
        // Set the plugin broker before loading any plugins.  
        // This is important for helper globa functions like get_plugin_ini 
        // which use the plugin broker when plugins are loaded
        Omeka_Context::getInstance()->setPluginBroker($broker);
        
        Zend_Registry::set('pluginloader', $pluginLoader);
        Zend_Registry::set('plugin_ini_reader', $pluginIniReader);
        Zend_Registry::set('plugin_mvc', $pluginMvc);
        
        $broker->register();
        $pluginLoader->loadPlugins($db->getTable('Plugin')->findAll());
        return $broker;
    }
}

<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Plugin\Broker
 */
class Omeka_Plugin_Broker_Factory
{
    private $_basePluginDir;

    public function __construct($basePluginDir)
    {
        $this->_basePluginDir = $basePluginDir;
    }

    public function getAll()
    {
        $pluginBroker = new Omeka_Plugin_Broker;   
        $pluginIniReader = new Omeka_Plugin_Ini($this->_basePluginDir);
        $pluginMvc = new Omeka_Plugin_Mvc($this->_basePluginDir);
        $pluginLoader = new Omeka_Plugin_Loader($pluginBroker, 
                                                $pluginIniReader,
                                                $pluginMvc,
                                                $this->_basePluginDir);
        $set = array(
            'pluginbroker' => $pluginBroker,
            'plugin_loader' => $pluginLoader,
            'plugin_ini_reader' => $pluginIniReader,
            'plugin_mvc' => $pluginMvc,
        );
        $this->_register($set);
        return $set;
    }

    private function _register($objs)
    {
        foreach ($objs as $k => $v) {
            Zend_Registry::set($k, $v);
        }
    }
}

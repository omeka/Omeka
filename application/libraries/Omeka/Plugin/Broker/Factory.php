<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
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

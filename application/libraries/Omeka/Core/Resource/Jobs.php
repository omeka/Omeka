<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Bootstrap resource for configuring the job dispatcher.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Core_Resource_Jobs extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_ADAPTER = "Omeka_Job_Dispatcher_Adapter_Synchronous";

    public function init()
    {
        $this->getBootstrap()->bootstrap('Config');
        $this->getBootstrap()->bootstrap('Db');
        $this->getBootstrap()->bootstrap('Currentuser');
        $config = $this->getBootstrap()->config->jobs;
        $adapterClass = self::DEFAULT_ADAPTER;
        $adapterOptions = array();
        if ($config) {
            if (isset($config->dispatcher)) {
                $adapterClass = $config->dispatcher;
            }
            if (isset($config->adapterOptions)) {
                $adapterOptions = $config->adapterOptions->toArray();
            }
        }
        if (!class_exists($adapterClass, true)) {
            throw new Omeka_Core_Resource_Jobs_InvalidAdapterException("Cannot find job dispatcher adapter class named '$adapterClass'.");
        }
        $adapter = new $adapterClass($adapterOptions);
        if (!($adapter instanceof Omeka_Job_Dispatcher_Adapter)) {
            throw new Omeka_Core_Resource_Jobs_InvalidAdapterException("Adapter named '$adapterClass' does not implement the required Omeka_Job_Dispatcher_Adapter interface.");
        }
        $dispatcher = new Omeka_Job_Dispatcher_Default($adapter, $this->getBootstrap()->currentuser);
        $factory = new Omeka_Job_Factory(array(
            'db'            => $this->getBootstrap()->db,
            'jobDispatcher' => $dispatcher,
        ));
        Zend_Registry::set('job_dispatcher', $dispatcher);
        Zend_Registry::set('job_factory', $factory);
        return $dispatcher;
    }
}

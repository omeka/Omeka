<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Bootstrap resource for configuring the job dispatcher.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Jobs extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_DISPATCHER = "Omeka_Job_Dispatcher_Adapter_Synchronous";
    const LONG_RUNNING_DISPATCHER = "Omeka_Job_Dispatcher_Adapter_BackgroundProcess";

    public function init()
    {
        $this->getBootstrap()->bootstrap('Config');
        $this->getBootstrap()->bootstrap('Db');
        $this->getBootstrap()->bootstrap('Currentuser');
        
        // Set the default dispatchers.
        $defaultClass = self::DEFAULT_DISPATCHER;
        $longRunningClass = self::LONG_RUNNING_DISPATCHER;
        
        // Get the dispatcher configurations.
        $config = $this->getBootstrap()->config->jobs;
        $defaultOptions = array();
        $longRunningOptions = array();
        if ($config) {
            if (isset($config->dispatcher->default)) {
                $defaultClass = $config->dispatcher->default;
            }
            if (isset($config->dispatcher->longRunning)) {
                $longRunningClass = $config->dispatcher->longRunning;
            }
            if (isset($config->dispatcher->defaultOptions)) {
                $defaultOptions = $config->dispatcher->defaultOptions->toArray();
            }
            if (isset($config->dispatcher->longRunningOptions)) {
                $longRunningOptions = $config->dispatcher->longRunningOptions->toArray();
            }
        }
        
        // Validate the dispatcher classes.
        if (!class_exists($defaultClass)) {
            throw new Omeka_Application_Resource_Jobs_InvalidAdapterException("Cannot find job dispatcher adapter class named \"$defaultClass\".");
        }
        if (!class_exists($longRunningClass)) {
            throw new Omeka_Application_Resource_Jobs_InvalidAdapterException("Cannot find job dispatcher adapter class named \"$longRunningClass\".");
        }
        
        // Instantiate the dispatcher objects.
        $default = new $defaultClass($defaultOptions);
        $longRunning = new $longRunningClass($longRunningOptions);
        
        // Validate the dispatcher objects.
        if (!($default instanceof Omeka_Job_Dispatcher_Adapter_AdapterInterface)) {
            throw new Omeka_Application_Resource_Jobs_InvalidAdapterException("Adapter named \"$defaultClass\" does not implement the required Omeka_Job_Dispatcher_Adapter_AdapterInterface interface.");
        }
        if (!($longRunning instanceof Omeka_Job_Dispatcher_Adapter_AdapterInterface)) {
            throw new Omeka_Application_Resource_Jobs_InvalidAdapterException("Adapter named \"$longRunningClass\" does not implement the required Omeka_Job_Dispatcher_Adapter_AdapterInterface interface.");
        }
        
        // Register the job dispatcher.
        $dispatcher = new Omeka_Job_Dispatcher_Default($default, $longRunning, 
            $this->getBootstrap()->currentuser);
        Zend_Registry::set('job_dispatcher', $dispatcher);
        
        // Register the job factory.
        $factory = new Omeka_Job_Factory(array(
            'db' => $this->getBootstrap()->db,
            'jobDispatcher' => $dispatcher,
        ));
        Zend_Registry::set('job_factory', $factory);
        
        // Return the job dispatcher.
        return $dispatcher;
    }
}

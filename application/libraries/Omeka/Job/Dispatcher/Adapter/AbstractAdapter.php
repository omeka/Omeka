<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Abstract class for job dispatcher adapters.
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
abstract class Omeka_Job_Dispatcher_Adapter_AbstractAdapter implements Omeka_Job_Dispatcher_Adapter_AdapterInterface
{
    private $_options = array();

    /**
     * @param array|null $options Optional Options to instantiate in the adapter.
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->_setOptions($options);
        }
    }

    private function _setOptions(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Retrieve an option by name as it was passed to the constructor of the 
     * adapter.
     *
     * @param string $name
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->_options)) {
            throw new Omeka_Job_Dispatcher_Adapter_RequiredOptionException($name);
        }
        return $this->_options[$name];
    }

    /**
     * Whether or not the given option has been set.
     *
     * @param string $name
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->_options);
    }

    /**
     * Adapter implementations do not understand named queues by default, so 
     * this default implementation returns false.  Override this in subclasses 
     * to specify the correct behavior.
     */
    public function setQueueName($name)
    {
        return false;
    }
}

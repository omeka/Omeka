<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Dispatcher for Zend_Queue.
 *
 * This would be particularly useful for installations that want to interface 
 * with ActiveMQ or Zend Server's Job Queue via Zend_Queue.  Note that using 
 * the 'Array' adapter should only be used for testing, as all jobs passed to 
 * it will be thrown away. 
 *
 * Required options include 'adapter' and 'options', which 
 * correspond to the first and second arguments to Zend_Queue's constructor 
 * respectively.
 *
 * For example, it would be configured like so in config.ini:
 * <code>
 * jobs.dispatcher = "Omeka_Job_Dispatcher_ZendQueue"
 * jobs.adapterOptions.adapter = "PlatformJobQueue"
 * jobs.adapterOptions.options.host = "127.0.0.1"
 * jobs.adapterOptions.options.password = "foobar"
 * </code>
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
class Omeka_Job_Dispatcher_Adapter_ZendQueue extends Omeka_Job_Dispatcher_Adapter_AbstractAdapter
{
    private $_queue;

    /**
     * Note that some Zend_Queue implementations understand the concept of 
     * named queues, while others do not.
     */
    public function setQueueName($name)
    {
        $this->_queue()->setOption(Zend_Queue::NAME, $name);       
    }

    public function send($encodedJob, array $metadata)
    {
        $this->_queue()->send($encodedJob);
    }

    private function _queue()
    {
        if (!$this->_queue) {
            $this->_queue = new Zend_Queue($this->getOption('adapter'), 
                                           $this->getOption('options'));
        }
        return $this->_queue;
    }
}

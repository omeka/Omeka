<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Dispatches jobs in Omeka.
 *
 * This provides a clean interface to adapter classes that deal with the details 
 * of how to dispatch jobs. It is initialized in the Jobs bootstrap resource and 
 * can be accessed via the registry. 
 *
 * Standard usage, where Job_Class_Name corresponds to a valid class name for a 
 * class implementing Omeka_JobInterface:
 *
 * <code>
 * $dispatcher = Zend_Registry::get('job_dispatcher');
 * $dispatcher->send('Job_Class_Name', array(
 *      'firstOption' => 'text',
 *      'secondOption' => 2
 * ));
 * </code>
 * 
 * @package Omeka\Job\Dispatcher
 */
class Omeka_Job_Dispatcher_Default implements Omeka_Job_Dispatcher_DispatcherInterface
{
    /**
     * @var Omeka_Job_Dispatcher_Adapter_AdapterInterface
     */
    private $_defaultAdapter;
    
    /**
     * @var Omeka_Job_Dispatcher_Adapter_AdapterInterface
     */
    private $_longRunningAdapter;
    
    /**
     * @var User
     */
    private $_user;
    
    /**
     * @param Omeka_Job_Dispatcher_Adapter_AdapterInterface $defaultAdapter
     * @param Omeka_Job_Dispatcher_Adapter_AdapterInterface $longRunningAdapter
     * @param User|null $user The user account associated with the request,
     * i.e. the user account associated with jobs sent by the dispatcher.
     */
    public function __construct(Omeka_Job_Dispatcher_Adapter_AdapterInterface $defaultAdapter, 
        Omeka_Job_Dispatcher_Adapter_AdapterInterface $longRunningAdapter, $user) {
        $this->setDefaultAdapter($defaultAdapter);
        $this->setLongRunningAdapter($longRunningAdapter);
        $this->setUser($user);
    }
    
    /**
     * Set the user.
     * 
     * @param User|null $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }
    
    /**
     * Get the user.
     * 
     * @return User|null
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Set the default adapter.
     * 
     * @param Omeka_Job_Dispatcher_Adapter_AdapterInterface $adapter
     */
    public function setDefaultAdapter(Omeka_Job_Dispatcher_Adapter_AdapterInterface $defaultAdapter)
    {
        $this->_defaultAdapter = $defaultAdapter;
    }
    
    /**
     * Set the long running adapter.
     * 
     * @param Omeka_Job_Dispatcher_Adapter_AdapterInterface $adapter
     */
    public function setLongRunningAdapter(Omeka_Job_Dispatcher_Adapter_AdapterInterface $longRunningAdapter)
    {
        $this->_longRunningAdapter = $longRunningAdapter;
    }
    
    /**
     * Set the name of the queue to which default jobs will be sent.
     *
     * NOTE: This may be ignored by adapters that do not understand the notion 
     * of named queues (or queues in general).
     *
     * @param string $name
     */
    public function setQueueName($name)
    {
        $this->_defaultAdapter->setQueueName($name);
    }
    
    /**
     * Set the name of the queue to which long-running jobs will be sent.
     *
     * NOTE: This may be ignored by adapters that do not understand the notion 
     * of named queues (or queues in general).
     *
     * @param string $name
     */
    public function setQueueNameLongRunning($name)
    {
        $this->_longRunningAdapter->setQueueName($name);
    }
    
    /**
     * Dispatch a job using the default dispatcher.
     * 
     * @param string $jobClass Class name that implements Omeka_JobInterface.
     * @param array $options Optional associative array containing options that 
     * the task needs in order to do its job. Note that all options should be 
     * primitive data types (or arrays containing primitive data types).
     */
    public function send($jobClass, $options = array())
    {
        $metadata = $this->_getJobMetadata($jobClass, $options);
        $this->_defaultAdapter->send($this->_toJson($metadata), $metadata);
    }
    
    /**
     * Dispatch a job using the long-running dispatcher.
     * 
     * @param string $jobClass Name of a class that implements Omeka_JobInterface.
     * @param array $options Optional associative array containing options that 
     * the task needs in order to do its job. Note that all options should be 
     * primitive data types (or arrays containing primitive data types).
     */
    public function sendLongRunning($jobClass, $options = array())
    {
        $metadata = $this->_getJobMetadata($jobClass, $options);
        $this->_longRunningAdapter->send($this->_toJson($metadata), $metadata);
    }

    private function _getJobMetadata($class, $options)
    {
        return array(
            'className'     => $class,
            'options'       => $options,
            'createdAt'     => Zend_Date::now(),
            'createdBy'     => $this->_user,
        );
    }

    private function _toJson($metadata)
    {
        $encodable = array(
            'className' => $metadata['className'],
            'options'   => $metadata['options'],
            'createdAt' => $metadata['createdAt']->toString(Zend_Date::ISO_8601),
        );
        if ($metadata['createdBy'] instanceof User) {
            $encodable['createdBy'] = $metadata['createdBy']->id;
        }
        return Zend_Json::encode($encodable);
    }
}

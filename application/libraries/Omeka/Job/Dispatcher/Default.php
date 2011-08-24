<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Dispatches jobs in Omeka.
 *
 * This provides a clean interface to adapter classes that deal with the
 * details of how to dispatch jobs.  It is initialized in the Jobs 
 * bootstrap resource and can be accessed via the registry. 
 *
 * Standard usage, where Job_Class_Name corresponds to a valid class
 * name for a class implementing Omeka_JobInterface:
 *
 * <code>
 * $dispatcher = Zend_Registry::get('job_dispatcher');
 * $dispatcher->send('Job_Class_Name', array(
 *      'firstOption' => 'text',
 *      'secondOption' => 2
 * ));
 * </code>
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Job_Dispatcher_Default implements Omeka_Job_Dispatcher
{
    /**
     * @var Omeka_Job_Dispatcher_Adapter
     */
    private $_adapter;

    private $_user;

    /**
     * @param Omeka_Job_Dispatcher_Adapter $adapter
     * @param User|null $user The user account associated with the request,
     * i.e. the user account associated with jobs sent by the dispatcher.
     */
    public function __construct(Omeka_Job_Dispatcher_Adapter $adapter, 
                                $user)
    {
        $this->setAdapter($adapter);
        $this->setUser($user);
    }

    /**
     * @param User|null $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param Omeka_Job_Dispatcher_Adapter $adapter
     */
    public function setAdapter(Omeka_Job_Dispatcher_Adapter $adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * Set the name of the queue to which jobs will be sent.
     *
     * NOTE: This may be ignored by adapters that do not understand the notion 
     * of named queues (or queues in general).
     *
     * @param string $name
     */
    public function setQueueName($name)
    {
        $this->_adapter->setQueueName($name);
    }

    /**
     * @param string $jobClass Name of a class that implements 
     * Omeka_JobInterface.
     * @param array $options Optional Associative array containing options
     * that the task needs in order to do its job.  Note that all options
     * should be primitive data types (or arrays containing primitive data
     * types).
     */
    public function send($jobClass, $options = array())
    {
        $metadata = $this->_getJobMetadata($jobClass, $options);
        $this->_adapter->send($this->_toJson($metadata), $metadata);
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

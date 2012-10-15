<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Abstract implementation of an Omeka job.
 *
 * Most plugin implementations of jobs will extend this class to gain convenient 
 * access to the database and other potentially important resources.
 *
 * @see Omeka_Job_Dispatcher_DispatcherInterface On how to dispatch jobs.
 * @package Omeka\Job
 */
abstract class Omeka_Job_AbstractJob implements Omeka_Job_JobInterface
{
    /**
     * @var Omeka_Db
     */
    protected $_db;
    
    /**
     * @var Omeka_Job_Dispatcher_DispatcherInterface
     */
    protected $_dispatcher;

    /**
     * @var User
     */
    protected $_user;

    protected $_options = array();

    public function __construct(array $options)
    {
        $this->_setOptions($options);
    }

    /**
     * Set all the options associated with this task.
     *
     * This is a convenience method that calls setter methods for the options 
     * given in the array.  If an element in the array does not have an 
     * associated setter method, it will be passed into the options array.
     */
    private function _setOptions(array $options)
    {
        $this->_options = $options;
        foreach ($options as $optionName => $optionValue) {
            $setMethodName = 'set' . ucwords($optionName);
            if (method_exists($this, $setMethodName)) {
                $this->{$setMethodName}($optionValue);
            }
        }
        unset($this->_options['jobDispatcher']);
        unset($this->_options['db']);
        unset($this->_options['user']);
    }

    public function setDb(Omeka_Db $db)
    {
        $this->_db = $db;
    }

    public function setJobDispatcher(Omeka_Job_Dispatcher_DispatcherInterface $dispatcher)
    {
        $this->_dispatcher = $dispatcher;
    }

    /**
     * Set the given User object on the Job object.
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->_user = $user;
    }

    /**
     * Get the User currently set on this Job, if any.
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Resend the job using the same options that were passed to the current 
     * job.
     */
    public function resend()
    {
        return $this->_dispatcher->send(get_class($this), $this->_options);
    }
}

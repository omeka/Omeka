<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Store dispatched jobs in an array.
 *
 * This is used primarily by unit tests and should not be used in production 
 * code.
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
class Omeka_Job_Dispatcher_Adapter_Array implements Omeka_Job_Dispatcher_Adapter_AdapterInterface
{
    private $_queueName = 'default';

    private $_jobs = array();

    public function setQueueName($name)
    {
        $this->_queueName = $name;
    }

    public function send($encodedJob, array $metadata)
    {
        $this->_jobs[] = array(
            'encoded' => $encodedJob,
            'metadata' => $metadata,
            'queue' => $this->_queueName,
        );
    }

    public function getJobs()
    {
        return $this->_jobs;
    }

    public function getJob($index = 0)
    {
        return $this->_jobs[$index];
    }
}

<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Dispatcher for executing jobs in real-time, i.e. executing within the browser 
 * request. 
 *
 * WARNING: While especially useful for simple jobs or instances where it is not 
 * possible to use one of the other adapters, keep in mind that long jobs may 
 * lead to request timeouts or open the possibility of DoS attacks by malicious 
 * users.
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
class Omeka_Job_Dispatcher_Adapter_Synchronous extends Omeka_Job_Dispatcher_Adapter_AbstractAdapter
{
    public function send($encodedJob, array $metadata)
    {
        $job = Zend_Registry::get('job_factory')->from($encodedJob);
        $job->perform();
        // Return the job for test purposes.
        return $job;
    }
}


<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Dispatcher for executing jobs in real-time, i.e. executing within the 
 * browser request. 
 *
 * WARNING: While especially useful for simple jobs or instances where it is 
 * not possible to use one of the other adapters, keep in mind that long jobs 
 * may lead to request timeouts or open the possibility of DoS attacks by 
 * malicious users.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Job_Dispatcher_Adapter_Synchronous extends Omeka_Job_Dispatcher_AdapterAbstract
{
    public function send($encodedJob, array $metadata)
    {
        $job = Zend_Registry::get('job_factory')->from($encodedJob);
        $job->perform();
        // Return the job for test purposes.
        return $job;
    }
}


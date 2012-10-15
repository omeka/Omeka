<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Interface for job dispatcher adapters.
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
interface Omeka_Job_Dispatcher_Adapter_AdapterInterface
{
    /**
     * Set the name of the queue that the adapter will use for incoming jobs.
     *
     * Note that this will not be used by some adapters and should be 
     * implemented to return false in those cases.
     *
     * @param string $name
     */
    public function setQueueName($name);
    
    /**
     * Send the job to whatever underlying system is used by the adapter.
     *
     * @param string $encodedJob The job encoded as a string.  In most cases, 
     * this will be passed directly into whatever client or queue the adapter 
     * uses.
     * @param array $metadata An array containing all the metadata for the job.  
     * This is the unencoded version of the first argument and exists as 
     * a convenience so that adapter writers do not have to attempt to decode 
     * the first argument manually. This array contains the following keys:
     *  <ul>
     *      <li>className - Corresponds to the class name of the job.</li>
     *      <li>options - Options that are passed to the job when it is 
     *      instantiated.</li>
     *      <li>createdBy - User object (or null) corresponding to the user 
     *      who created this job.</li>
     *      <li>createdAt - Zend_Date corresponding to the date/time at which 
     *      this job was created.</li>
     *  </ul>
     */
    public function send($encodedJob, array $metadata);
}

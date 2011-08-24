<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Interface for job dispatchers in Omeka.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
interface Omeka_Job_Dispatcher
{
    /**
     * Set the name of the queue to which jobs will be sent.
     *
     * NOTE: This may be ignored by adapters that do not understand the notion 
     * of named queues (or queues in general).
     *
     * @param string $name
     */
    public function setQueueName($name);

    /**
     * @param string $jobClass Name of a class that implements 
     * Omeka_JobInterface.
     * @param array $options Optional Associative array containing options
     * that the task needs in order to do its job.  Note that all options
     * should be primitive data types (or arrays containing primitive data
     * types).
     */
    public function send($jobClass, $options = array());
}

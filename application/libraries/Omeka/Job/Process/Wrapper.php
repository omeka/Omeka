<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Wrapper that allows Omeka_Job to work with the existing Process/
 * Omeka_Job_Process_Dispatcher API. Jobs are passed in as the 'job' argument, 
 * and this wrapper handles decoding and executing the job.
 *
 * @package Omeka\Job\Process
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Job_Process_Wrapper extends Omeka_Job_Process_AbstractProcess
{
    private function _getJob($str)
    {
        return Zend_Registry::get('job_factory')->from($str);
    }

    /**
     * Args passed in will consist of the JSON-encoded task.
     */
    public function run($args)
    {
        $job = $this->_getJob($args['job']);
        $job->perform();
    }
}

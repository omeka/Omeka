<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Job dispatcher that uses Omeka's existing background process API.
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
class Omeka_Job_Dispatcher_Adapter_BackgroundProcess extends 
    Omeka_Job_Dispatcher_Adapter_AbstractAdapter {
        
    private $_processDispatcher;

    /**
     * Dispatches a background process that executes the given job. 
     *
     * NOTE: No user account is bootstrapped when background.php runs (since it 
     * is CLI), so if a process triggers its own subprocesses, those will be 
     * listed as belonging to no user (ID = 0).       
     *
     * @see Omeka_Job_Process_Wrapper
     */
    public function send($encodedJob, array $metadata)
    {
        $this->getProcessDispatcher()->startProcess('Omeka_Job_Process_Wrapper', 
            $metadata['createdBy'], array('job' => $encodedJob));
    }

    /**
     * For test purposes.
     */
    public function setProcessDispatcher(Omeka_Job_Process_Dispatcher $dispatcher)
    {
        $this->_processDispatcher = $dispatcher;
    }

    public function getProcessDispatcher()
    {
        if (!$this->_processDispatcher) {
            $this->_processDispatcher = new Omeka_Job_Process_Dispatcher;
        }
        return $this->_processDispatcher;
    }
}

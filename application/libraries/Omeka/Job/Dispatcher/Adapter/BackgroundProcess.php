<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Job dispatcher that uses Omeka's existing background process API.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Job_Dispatcher_Adapter_BackgroundProcess extends 
Omeka_Job_Dispatcher_AdapterAbstract
{
    private $_processDispatcher;

    /**
     * Dispatches a background process that executes the given job. 
     *
     * NOTE: No user account is bootstrapped when background.php runs (since it 
     * is CLI), so if a process triggers its own subprocesses, those will be 
     * listed as belonging to no user (ID = 0).       
     *
     * @see Omeka_Job_ProcessWrapper
     */
    public function send($encodedJob, array $metadata)
    {
        $this->getProcessDispatcher()->startProcess('Omeka_Job_ProcessWrapper', 
            $metadata['createdBy'], array('job' => $encodedJob));
    }

    /**
     * For test purposes.
     */
    public function setProcessDispatcher(ProcessDispatcher $dispatcher)
    {
        $this->_processDispatcher = $dispatcher;
    }

    public function getProcessDispatcher()
    {
        if (!$this->_processDispatcher) {
            $this->_processDispatcher = new ProcessDispatcher;
        }
        return $this->_processDispatcher;
    }
}

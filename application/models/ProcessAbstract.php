<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Base class background processes descend from.
 * 
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */ 
abstract class ProcessAbstract
{
    protected $_process;
    protected $_logger;
    
    final public function __construct(Process $process, $logger) 
    {        
        $this->_process = $process;
        $this->_process->pid = getmypid();
        $this->_process->status = Process::STATUS_IN_PROGRESS;
        $this->_process->save();
        
        // Set the logger property.
        if ($logger instanceof Zend_Log) {
            $this->_logger = $logger;
        }
    }
    
    final public function __destruct() 
    {
        $this->_process->pid = null;
        if (Process::STATUS_ERROR != $this->_process->status) {
            $this->_process->status = Process::STATUS_COMPLETED;
        }
        $this->_process->stopped = date('Y-m-d G:i:s');
        $this->_process->save();
    }
    
    protected function _log($message, $priority = null)
    {
        // Do not log if the logger object is not set.
        if (!($this->_logger instanceof Zend_Log)) {
            return;
        }
        
        // Set the priority of the message.
        if (!$priority) {
            $priority = Zend_Log::INFO;
        }
        
        // Log the message.
        $this->_logger->log($message, $priority);
    }
    
    abstract public function run($args);
}

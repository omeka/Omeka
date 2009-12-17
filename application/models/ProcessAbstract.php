<?php 
/**
 * Base class background processes descend from.
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */
 
abstract class ProcessAbstract
{
    protected $_process;
    
    final public function __construct(Process $process) 
    {        
        $this->_process = $process;
        $this->_process->pid = getmypid();
        $this->_process->status = Process::STATUS_IN_PROGRESS;
        $this->_process->save();
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
    
    abstract public function run($args);
}
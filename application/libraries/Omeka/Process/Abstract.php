<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Base class background processes descend from.
 * @package Omeka
 */
abstract class Omeka_Process_Abstract
{
    private $_process;
    
    final public function __construct(Process $process) {
        $this->_process = $process;
        $this->_process->pid = getmypid();
        $this->setStatus(Process::STATUS_IN_PROGRESS);
        $this->_process->save();
    }
    
    abstract public function run();
    
    public function setStatus() {
        
    }
}
<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A process and its metadata.
 * 
 * @package Omeka\Record
 */
class Process extends Omeka_Record_AbstractRecord
{
    const STATUS_STARTING = 'starting';
    const STATUS_IN_PROGRESS = 'in progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAUSED = 'paused';
    const STATUS_ERROR = 'error';
    const STATUS_STOPPED = 'stopped';
    
    public $pid;
    public $class;
    public $user_id;
    public $status;
    public $args;
    public $started;
    public $stopped;
    
    protected function beforeSave($args)
    {
        if (!$this->_isSerialized($this->args)) {
            $this->args = serialize($this->args);            
        }
    }
    
    public function getArguments()
    {     
        if ($this->_isSerialized($this->args)) {
            $this->args = unserialize($this->args);
        }        
        return $this->args;
    }
    
    public function setArguments($args)
    {
        $this->args = $args;
    }
    
    private function _isSerialized($s)
    {
        if (!is_string($s)) {
            return false;
        }
        return (($s === 'b:0;') || (@unserialize($s) !== false));
    }
}

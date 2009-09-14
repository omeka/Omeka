<?php 
/**
 * The model class that holds generic process data
 *
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */

class Process extends Omeka_Record
{
    const STATUS_STARTING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_PAUSED = 4;
    const STATUS_ERROR = 5;
    
    public $pid;
    public $class;
    public $user_id;
    public $status;
    public $args;
    
    protected function beforeSave()
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
        return (($s === 'b:0;') || (@unserialize($s) !== false));
    }
}
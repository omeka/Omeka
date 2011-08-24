<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * The model class that holds generic process data
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @subpackage Models
 */
class Process extends Omeka_Record
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
        if (!is_string($s)) {
            return false;
        }
        return (($s === 'b:0;') || (@unserialize($s) !== false));
    }
}

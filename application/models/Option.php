<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Option extends Omeka_Record { 
    public $name;
    public $value;
    
    public function __toString() {
        return $this->value;
    }
    
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', 'Each option must have a name.');
        }
        
        if (!$this->fieldIsUnique('name')) {
            $this->addError('name', 'Each option must have a unique name.');
        }
    }
}
<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An option and its metadata.
 * 
 * @package Omeka\Record
 */
class Option extends Omeka_Record_AbstractRecord { 
    public $name;
    public $value;
    
    public function __toString() {
        return $this->value;
    }
    
    protected function _validate()
    {
        if (empty($this->name)) {
            $this->addError('name', __('Each option must have a unique name.'));
        }
        
        if (!$this->fieldIsUnique('name')) {
            $this->addError('name', __('Each option must have a unique name.'));
        }
    }
}

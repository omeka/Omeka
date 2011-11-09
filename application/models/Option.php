<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Option extends Omeka_Record { 
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

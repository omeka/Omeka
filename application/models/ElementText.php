<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * An element text and its metadata.
 * 
 * @package Omeka\Record
 */
class ElementText extends Omeka_Record_AbstractRecord
{
    public $record_id;
    public $record_type;
    public $element_id;
    public $html = 0;
    public $text;

    /**
     * Validate the element text prior to saving.
     */
    protected function _validate()
    {
        if ($this->record_id < 1) {
            $this->addError('record_id', __('Invalid record ID.'));
        }
        if (empty($this->record_type)) {
            $this->addError('record_type', __('All element texts must have a record type.'));
        }
        if ($this->element_id < 1) {
            $this->addError('element_id', __('Invalid element ID.'));
        }
    }

    public function __toString()
    {
        return (string) $text;
    }    
    
    public function setText($text)
    {
        $this->text = (string) $text;
    }
    
    public function getText()
    {
        return (string) $this->text;
    }
    
    public function isHtml()
    {
        return (boolean) $this->html;
    }
}

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
    /**
     * ID of the associated record.
     *
     * @var int
     */
    public $record_id;

    /**
     * Type of the associated record.
     *
     * @var string
     */
    public $record_type;

    /**
     * ID of this text's Element.
     *
     * @var int
     */
    public $element_id;

    /**
     * Whether this text is HTML.
     *
     * @var int
     */
    public $html = 0;

    /**
     * The text itself.
     *
     * @var string
     */
    public $text;

    /**
     * Validate the element text prior to saving.
     *
     * Test for a positive record_id and element_id, and a non-empty record_type.
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

    /**
     * Use the actual text when printing an ElementText as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getText();
    }

    /**
     * Set the text.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = (string) $text;
    }

    /**
     * Get the text.
     *
     * @return string
     */
    public function getText()
    {
        return (string) $this->text;
    }

    /**
     * Get whether this text is HTML.
     *
     * @return bool
     */
    public function isHtml()
    {
        return (boolean) $this->html;
    }
}

<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * A site option saved in the database.
 *
 * Options are stored and accessed by string keys.
 * 
 * @package Omeka\Record
 */
class Option extends Omeka_Record_AbstractRecord
{
    /**
     * Option name.
     *
     * @var string
     */
    public $name;

    /**
     * Option value.
     *
     * @var string
     */
    public $value;

    /**
     * Use the option's value when treating it as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    /**
     * Validate the Option.
     *
     * An option must have a non-empty and unique name.
     */
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

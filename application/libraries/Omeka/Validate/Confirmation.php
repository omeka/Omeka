<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Adapted from Zend Framework documentation on custom validators.
 * 
 * @package Omeka\Validate
 */
class Omeka_Validate_Confirmation extends Zend_Validate_Abstract
{
    /**
     * Error message for non-matching confirmation.
     */
    const NOT_MATCH = 'notMatch';
    
    /**
     * Field needing confirmation.
     *
     * @var string
     */
    protected $_field;
    
    /**
     * Error messages.
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Confirmation does not match'
    );
    
    /**
     * Error message replace variables.
     *
     * @var array
     */
    protected $_messageVariables = array(
        'field' => '_field'
    );
    
    /**
     * Sets validator options
     *
     * @param  mixed|Zend_Config $field
     */
    public function __construct($field)
    {
        if ($field instanceof Zend_Config) {
            $field = $field->toArray();
        }

        if (is_array($field)) {
            if (array_key_exists('field', $field)) {
                $field = $field['field'];
            } else {
                throw new Zend_Validate_Exception("Missing option 'field'");
            }
        }

        $this->setField($field);
    }

    
    /**
     * Check that the value is valid.
     *
     * @param string $value
     * @param string|array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if (is_array($context)) {
            $confirmFieldName = $this->getField();
            if (isset($context[$confirmFieldName])
                && ($value == $context[$confirmFieldName]))
            {
                return true;
            }
        } elseif (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
    
    /**
     * Get the name of the field that needs confirmation.
     *
     * @return string
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Set the name of the field that needs confirmation.
     * 
     * @param string $field
     * @return void
     */
    public function setField($field)
    {
        $this->_field = $field;
        return $this;
    }
}

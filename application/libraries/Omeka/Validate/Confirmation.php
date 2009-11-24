<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Adapted from Zend Framework documentation on custom validators.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Validate_Confirmation extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_field;

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Confirmation does not match'
    );
    
    protected $_messageVariables = array(
        'field' => '_field'
    );
    
    public function __construct($field)
    {
        $this->setField($field);
    }
    
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
     **/
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Set the name of the field that needs confirmation.
     * 
     * @param string $field
     **/
    public function setField($field)
    {
        $this->_field = $field;
        return $this;
    }
}
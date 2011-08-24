<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * This is an object wrapper for validation errors.  The primary advantage
 * to having this class is that casting it to a string will convert the errors
 * into a nicely formatted, human-readable string.
 *
 * @see Omeka_Record
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Validator_Errors extends ArrayObject
{
    /**
     * List of validation errors.
     *
     * @var array
     */
    protected $_errors = array();
    
    /**
     * @param array|null $errors Initial errors to set.
     */
    public function __construct($errors=null)
    {
        if ($errors) {
            $this->_errors = $errors;
        }
    }
    
    /**
     * Get an error from the list.
     * Required by ArrayObject.
     *
     * @param mixed $key Key into array.
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->_errors)) {
            return $this->_errors[$key];
        }
    }
    
    /**
     * Set an error into the list.
     * Required by ArrayObject.
     *
     * @param mixed $key Key into array.
     * @param mixed $val Value to store.
     */
    public function offsetSet($key, $val)
    {
        $this->_errors[$key] = $val;
    }
    
    /**
     * Get the array of errors.
     *
     * @see Omeka_Record::addErrorsFrom()
     * @return array
     */
    public function get()
    {
        return $this->_errors;
    }
    
    /**
     * Get the number of errors.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_errors);
    }
    
    /**
     * Get a string representation of all the stored errors.
     *
     * @return string
     */
    public function __toString()
    {
        $msgs = array();
        foreach ($this->_errors as $field => $error) {
            $msgs[] = (!is_numeric($field) ? (Inflector::humanize($field, 'all'). ": ") : '') . $error; 
        }
        
        return join("\n", $msgs);
    }
}

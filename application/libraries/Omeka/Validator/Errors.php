<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * This is an object wrapper for validation errors.  The primary advantage
 * to having this class is that casting it to a string will convert the errors
 * into a nicely formatted, human-readable string.
 *
 * @see Omeka_Record
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Validator_Errors extends ArrayObject
{
    protected $_errors = array();
    
    public function __construct($errors=null)
    {
        if ($errors) {
            $this->_errors = $errors;
        }
    }
    
    public function offsetGet($key)
    {
        return $this->_errors[$key];
    }
    
    public function offsetSet($key, $val)
    {
        $this->_errors[$key] = $val;
    }
    
    /**
     * Shouldn't there be an analog for this in ArrayObject?  Seems retarded.
     * @see Omeka_Record::addErrorsFrom()
     * @return array
     **/
    public function get()
    {
        return $this->_errors;
    }
    
    public function count()
    {
        return count($this->_errors);
    }
    
    public function __toString()
    {
        $msgs = array();
        foreach ($this->_errors as $field => $error) {
            $msgs[] = (!is_numeric($field) ? (Inflector::humanize($field, 'all'). ": ") : '') . $error; 
        }
        
        return join("\n", $msgs);
    }
}

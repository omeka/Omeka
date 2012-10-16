<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Exception that is thrown when a form could not be validated correctly.
 * 
 * @package Omeka\Validate
 */
class Omeka_Validate_Exception extends Exception
{
    /**
     * Message representing form errors.
     *
     * @var string
     */
    protected $_errors = array();

    /**
     * @param $errors string|Omeka_Validate_Errors If a string, it is a
     * single error.  If it is an instance of Omeka_Validate_Errors, it is
     * a set of errors.
     * @return void
     */
    public function __construct($errors)
    {
        $this->_errors = $errors;

        if ($errors instanceof Omeka_Validate_Errors) {
            $this->message = (string) $errors;
        } else if (is_string($errors)) {
            $this->message = $errors;
        }
    }

    /**
     * Get the error message that caused this exception.
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}

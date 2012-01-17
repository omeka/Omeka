<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Exception that is thrown when a form could not be validated correctly.
 *
 * @todo If possible to use Zend_Form for form generation instead of this
 * class, then this class and Omeka_Validator_Errors will be deprecated
 * in favor of built-in Zend Framework capabilities.
 * @see Omeka_Record::saveForm()
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Validator_Exception extends Exception
{
    /**
     * Message representing form errors.
     *
     * @var string
     */
    protected $_errors = array();

    /**
     * @param $errors string|Omeka_Validator_Errors If a string, it is a
     * single error.  If it is an instance of Omeka_Validator_Errors, it is
     * a set of errors.
     * @return void
     */
    public function __construct($errors)
    {
        $this->_errors = $errors;

        if ($errors instanceof Omeka_Validator_Errors) {
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

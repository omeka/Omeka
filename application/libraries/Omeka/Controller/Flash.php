<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * This is a replacement for Zend Framework's FlashMessenger
 * that handles form validation errors and categorizes messages according
 * to their status (currently SUCCESS, ERROR, ALERT).
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @todo Refactor this so that it subclasses Zend's FlashMessenger action
 * helper (adding message status) and use Zend_Form's form validation
 * instead of the form validation in this class.
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_Flash
{
    /**
     * Status: Indicates a successful operation.
     */
    const SUCCESS          = 1;

    /**
     * Status: Indicates an error during form or model validation.
     */
    const VALIDATION_ERROR = 2;

    /**
     * Status: Indicates a general error.
     */
    const GENERAL_ERROR    = 3;

    /**
     * Status: Indicates an alert or warning.
     */
    const ALERT            = 4;

    /**
     * Priority: This message should be displayed immediately.
     */
    const DISPLAY_NOW      = 5;

    /**
     * Priority: This message should be displayed during the next request.
     */
    const DISPLAY_NEXT     = 6;

    /**
     * The session object that stores the flash values.
     *
     * @var Zend_Session_Namespace
     */
    protected static $_session;

    /**
     * Whether or not to display the flash during the current request or the next
     *
     * @var integer
     */
    protected static $_priority;

    /**
     * Useful for storing message state outside the session
     * used for self::DISPLAY_NOW priority
     *
     * @var stdClass
     */
    protected static $_flash;

    /**
     * Create 'flash' session namespace for storing flash messages.
     */
    public function __construct()
    {
        if (!self::$_session) {
            self::$_session = new Zend_Session_Namespace('flash');
        }
    }

    /**
     * Set a flash message.
     *
     * @param integer $status_code Status code; see class constants.
     * @param string|array $msg Either a string message to flash or a keyed array
     * where keys are the fields in the form.
     * @param integer priority When to display the message; see class constants.
     * @return void
     */
    public function setFlash($status_code, $msg, $priority=null)
    {
        self::$_priority = (!$priority) ? self::DISPLAY_NEXT : $priority;

        switch (self::$_priority) {
            case self::DISPLAY_NOW:
                $this->setTempFlash($status_code, $msg);
                break;
            default:
                self::$_session->msg = $msg;
                self::$_session->status = $status_code;
                break;
        }
    }

    /**
     * Set the temporary flash variables.
     *
     * Variables set here are stored in this instance, not the session.
     *
     * @param integer $status Status code; see class constants.
     * @param string|array $msg Either a string message to flash or a keyed array
     * where keys are the fields in the form.
     * @return void
     */
    protected function setTempFlash($status, $msg)
    {
        self::$_flash = new stdClass;
        self::$_flash->msg = $msg;
        self::$_flash->status = $status;
    }

    /**
     * Retrieve the flash info.
     *
     * @return stdClass|Zend_Session_Namespace
     */
    protected function getFlash()
    {
        if (self::$_flash instanceof stdClass) {
            return self::$_flash;
        }

        return self::$_session;
    }

    /**
     * Retrieve the status code for the flash
     *
     * Possible status codes are class constants:
     * -SUCCESS
     * -VALIDATION_ERROR
     * -GENERAL_ERROR
     * -ALERT
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->getFlash()->status;
    }

    /**
     * Retrieve a formatted version of the error/success message and then clean
     * out the session.
     * Note: errors are preserved within the temporary flash object.
     *
     * @return string
     */
    public function getMsg()
    {
        $msg = $this->getFlash()->msg;

        $this->resetFlash();

        return (string) $msg;
    }

    /**
     * Clear all static data.  Used primarily by test cases.
     *
     * @return void
     */
    public static function reset()
    {
        if (self::$_session instanceof Zend_Session_Namespace && isset($_SESSION)) {
            self::$_session->unsetAll();
        }
        self::$_priority = null;
        self::$_flash = null;
    }

    /**
     * Move the flash info from the session to the temporary class vars.
     * This way, the values can be retrieved for the rest of the request.
     *
     * @return void
     */
    protected function resetFlash()
    {
        if (($flash = $this->getFlash())) {
            if ($flash instanceof Zend_Session_Namespace) {
                $this->setTempFlash($flash->status, $flash->msg);
                unset($flash->msg);
                unset($flash->status);
            }
        }
    }

    /**
     * Return the error message for a specific form field.
     *
     * @param string $field Field name.
     * @return string
     */
    public function getError($field)
    {
        $msg = $this->getFlash()->msg;
        if ((is_array($msg) && array_key_exists($field, $msg)) ||
            $msg instanceof Omeka_Validator_Errors) {
            return $msg[$field];
        }
    }
}

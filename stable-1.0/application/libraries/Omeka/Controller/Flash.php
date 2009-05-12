<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * This is a replacement for Zend Framework's FlashMessenger
 * that handles form validation errors and categorizes messages according
 * to their status (currently SUCCESS, ERROR, ALERT). 
 *
 * @todo Refactor this so that it subclasses Zend's FlashMessenger action 
 * helper (adding message status) and use Zend_Form's form validation
 * instead of the form validation in this class.
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Omeka_Controller_Flash
{
	const SUCCESS          = 1;
	const VALIDATION_ERROR = 2;
	const GENERAL_ERROR    = 3;
	const ALERT            = 4;
	const DISPLAY_NOW      = 5;
	const DISPLAY_NEXT     = 6;
	
	/**
	 * The session object that stores the flash values
	 *
	 * @var Zend_Session_Namespace
	 **/
	protected static $_session;
	
	/**
	 * Whether or not to display the flash during the current request or the next
	 *
	 * @var int
	 **/
	protected static $_priority;
			
	/**
	 * Useful for storing message state outside the session
	 * used for self::DISPLAY_NOW priority
	 *
	 * @return void
	 **/	
	protected static $_flash;	
		
	public function __construct()
	{
		if (!$this->_session) {
			$this->_session = new Zend_Session_Namespace('flash');
		}		
	}
	
	/**
	 * 
	 * @param const
	 * @param string|array either a string message to flash or a keyed array where keys are the fields in the form
	 * @return void
	 **/
	public function setFlash($status_code, $msg, $priority=null)
	{
		$this->_priority = (!$priority) ? self::DISPLAY_NEXT : $priority;
		
		switch ($this->_priority) {
			case self::DISPLAY_NOW:
				$this->setTempFlash($status_code, $msg);
				break;
			default:
				$this->_session->msg = $msg;
				$this->_session->status = $status_code;
				break;
		}			
	}
	
	/**
	 * Set the temporary flash variables (temporary because they are stored in this obj instance and not the session)
	 *
	 * @return void
	 **/
	protected function setTempFlash($status, $msg)
	{
		self::$_flash = new stdClass;
		self::$_flash->msg = $msg;
		self::$_flash->status = $status;		
	}
	
	/**
	 * Retrieve the flash info
	 *
	 * @return stdClass|Zend_Session_Namespace
	 **/
	protected function getFlash()
	{
		if (self::$_flash instanceof stdClass) {
            return self::$_flash;
        }
		
		return $this->_session;
	}	
	
	/**
	 * Retrieve the status code for the flash
	 * 
	 * Possible status codes are class constants: SUCCESS, VALIDATION_ERROR, GENERAL_ERROR, ALERT
	 *
	 * @return int
	 **/
	public function getStatus()
	{
		return $this->getFlash()->status;
	} 
	
	/**
	 * Retrieve a formatted version of the error/success message and then clean out the session.
	 * Note: errors are preserved within the flash 
	 *
	 * @return string
	 **/
	public function getMsg()
	{
		$msg = $this->getFlash()->msg;
		
		$this->resetFlash();
				
		return (string) $msg;
	}
	
	/**
	 * Move the flash info from the session to the more temporary class vars (may need it for the rest of the request)
	 *
	 * @return void
	 **/
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
	 * Return the error message for a specific field
	 *
	 * @return string
	 **/
	public function getError($field)
	{
		$msg = $this->getFlash()->msg;
		if (is_array($msg)) {
		  return @$msg[$field];
		}
	}
}
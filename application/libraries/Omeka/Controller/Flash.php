<?php 
/**
* 
*/
class Omeka_Controller_Flash
{
	const SUCCESS = 1;
	const VALIDATION_ERROR = 2;
	const GENERAL_ERROR = 3;
	const ALERT = 4;
	
	const DISPLAY_NOW = 5;
	const DISPLAY_NEXT = 6;
	
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
		if(!$this->_session) {
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
		if(self::$_flash instanceof stdClass) return self::$_flash;
		
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
		
		if(is_array($msg)) {
			return $this->formatErrorsIntoNiceMessage($msg);
		}
		
		return $msg;
	}
	
	/**
	 * Move the flash info from the session to the more temporary class vars (may need it for the rest of the request)
	 *
	 * @return void
	 **/
	protected function resetFlash()
	{
		if( ($flash = $this->getFlash())) {
			if ($flash instanceof Zend_Session_Namespace) {
				$this->setTempFlash($flash->status, $flash->msg);
				unset($flash->msg);
				unset($flash->status);
			}
		}
	}
	
	/**
	 * Take an array of error messages and convert it into human-readable format
	 *
	 * @return string
	 **/
	protected function formatErrorsIntoNiceMessage($errors)
	{
		$msgs = array();
		foreach ($errors as $field => $error) {
	 
			$msgs[] = (!is_numeric($field) ? (Omeka::humanize($field). ": ") : '') . $error; 
		}

		return join("\n", $msgs);			
	}
	
	/**
	 * Return the error message for a specific field
	 *
	 * @return string
	 **/
	public function getError($field)
	{
		$msg = $this->getFlash()->msg;
		
		return (is_array($msg) and array_key_exists($field, $msg)) ? $msg[$field] : null;
	}
}
 
?>

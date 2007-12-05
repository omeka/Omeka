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
	

	protected static $_session;

	protected static $_priority;
	
	protected $_errors;
		
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
	
	protected function setTempFlash($status, $msg)
	{
		self::$_flash = new stdClass;
		self::$_flash->msg = $msg;
		self::$_flash->status = $status;		
	}
	
	protected function getFlash()
	{
		if(self::$_flash instanceof stdClass) return self::$_flash;
		
		return $this->_session;
	}	
	
	public function getStatus()
	{
		return $this->getFlash()->status;
	} 
	
	/**
	 * Retrieve a formatted version of the error/success message and then clean out the session.
	 * Note: errors are preserved within the flash 
	 *
	 * @return void
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
	
	//Move the flash info from the session to the more temporary class vars (may need it for the rest of the request)
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
	
	protected function formatErrorsIntoNiceMessage($errors)
	{
		$msgs = array();
		foreach ($errors as $field => $error) {
	 
			$msgs[] = (!is_numeric($field) ? (Omeka::humanize($field). ": ") : '') . $error; 
		}

		return join("\n", $msgs);			
	}
	
	public function getError($field)
	{
		$msg = $this->getFlash()->msg;
		
		return (is_array($msg) and array_key_exists($field, $msg)) ? $msg[$field] : null;
	}
}
 
?>

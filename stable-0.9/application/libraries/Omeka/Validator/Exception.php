<?php 
/**
* Validator_Exception
*/
class Omeka_Validator_Exception extends Exception
{
	protected $_errors = array();
	
	/**
	 * @param $errors array|string If an array, it is a set of errors.  If a string, it is a single error.
	 *
	 **/
	public function __construct($errors)
	{
		$this->_errors = $errors;
		
		if($errors instanceof Omeka_Validator_Errors) {
			$this->message = (string) $errors;
		}
		elseif(is_string($errors)) {
			$this->message = $errors;
		}
	}	
		
	public function getErrors()
	{
		return $this->_errors;
	}
}
 
?>

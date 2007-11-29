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
		if(is_array($errors)) {
			$this->_errors = $errors;
		}
		else {
			$this->_errors[] = $errors;
		}
		
		$this->convertToMessage();
	}	
	
	private function convertToMessage()
	{
		$msgs = array();
		foreach ($this->_errors as $field => $error) {
			 
			$msgs[] = (!is_numeric($field) ? (humanize($field). ": ") : '') . $error; 
		}
		
		$this->message = join("\n", $msgs);		
	}
	
	public function getErrors()
	{
		return $this->_errors;
	}
}
 
?>

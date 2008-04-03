<?php
/**
* 
*/
class Omeka_Validator_Errors extends ArrayObject
{
	protected $_errors = array();
	
	public function __construct($errors=null)
	{
		if($errors) {
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
	
	public function count()
	{
		return count($this->_errors);
	}
	
	public function __toString()
	{
		$msgs = array();
		foreach ($this->_errors as $field => $error) {
	 
			$msgs[] = (!is_numeric($field) ? (Omeka::humanize($field). ": ") : '') . $error; 
		}

		return join("\n", $msgs);
	}
}

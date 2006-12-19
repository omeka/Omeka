<?php
/**
 * Inspiration from Zend's Http Response Object
 */
class Kea_Controller_Response_Abstract
{	
	protected $_headers = array();
	
	protected $_body = "";
	
	protected $_exception;
	
	protected $_data = array();
	
	final public function __get($name)
	{
		if (array_key_exists($name, $this->_data)) {
			return $this->_data[$name];
		}
		return false;
	}
	
	final public function __set($name, $val)
	{
		$this->_data[$name] = $val;
	}
	
	public function appendBody($body)
	{
		$this->_body .= $body;
	}
	
	public function addHeader($type, $val)
	{
		$this->_headers[$type] = $val;
	}
	
	public function sendHeaders()
	{
		if (headers_sent()) {
			throw new Kea_Exception(
				"Cannot send the headers because they have already been sent"
			);
			return;
		}
		
		foreach ($this->_headers as $type => $val) {
			header($type . ": ".$val);
		}
	}
	
	public function setException(Exception $e)
	{
		$this->_exception = $e;
	}
	
	public function __toString()
	{
		if ($this->_exception instanceof Exception) {
			return $this->_exception->__toString();
		}
		$this->sendHeaders();
		return $this->_body;
	}
}

?>
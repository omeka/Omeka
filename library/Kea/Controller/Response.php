<?php
/**
 * Inspiration from Zend's Http Response Object
 */
class Kea_Controller_Response
{
	private static $_instance;
	
	private $_headers = array();
	
	private $_header;
	
	private $_body = "";
	
	private $_exception;
	
	private $_data;
	
	private function __construct() {}
	private function __clone() {}
	
	public static function getInstance()
	{
		if (!self::$_instance instanceof self) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	public function addHeader($type, $val)
	{
		$this->_headers[$type] = $val;
	}
	
	public function sendHeaders()
	{
		if (headers_sent()) {
			throw new Kea_Exception(
				"Cannot send the headers, because they have already been sent"
			);
			return;
		}
		
		foreach ($this->_headers as $type => $val) {
			header($type . ": ".$val);
		}
	}
	
	public function addBody($text)
	{
		$this->_body .= $text;
	}
	
	public function add($data)
	{
		$this->_data[] = $data;
		return $this;
	}
	
	public function setException(Exception $e)
	{
		$this->_exception = $e;
	}
	
	public function __toString()
	{
		if ($this->_exception instanceof Exception) {
			return $this->_exception->getMessage();
		}
		$this->sendHeaders();
		return $this->_body;
	}
}

?>
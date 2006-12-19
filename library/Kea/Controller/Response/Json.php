<?php
class Kea_Controller_Response_Json extends Kea_Controller_Response_Abstract
{
	public function __construct()
	{
		$this->addHeader("Content-type", "json");
	}
	
	public function __toString()
	{
		require_once 'Zend/Json.php';
		foreach ($this->_data as $data)
		{
			$this->appendBody(Zend_Json::encode($data));
		}
		return parent::__toString();
	}
}
?>
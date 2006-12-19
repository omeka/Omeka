<?php
class Kea_Controller_Response_Rest extends Kea_Controller_Response_Abstract
{
	public function __construct()
	{
		$this->addHeader("Content-type", "application/xml");
	}
	
	public function __toString()
	{
		return parent::__toString();
	}
}
?>
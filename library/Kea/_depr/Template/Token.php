<?php
/**
 * @edited 10/13/06
 */
require_once 'Kea/Token.php';

class Kea_Template_Token extends Kea_Token
{
	private $_template;
	
	public function getTemplate()
	{
		return $this->_template;
	}
	
	public function setTemplate($t)
	{
		$this->_template = $t;
	}
}

?>
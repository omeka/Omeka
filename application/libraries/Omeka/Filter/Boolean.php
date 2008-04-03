<?php 
class Omeka_Filter_Boolean implements Zend_Filter_Interface
{
	public function filter($value)
	{
		return in_array($value, array('true', 'On', 'on', 1, "1", true), true) ? "1" : "0";
	}
}	 
?>

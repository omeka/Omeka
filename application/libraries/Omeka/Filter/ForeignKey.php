<?php 
class Omeka_Filter_ForeignKey implements Zend_Filter_Interface
{
	public function filter($value)
	{
		if(empty($value) or ( (int) $value <= 0) ) {
			return null;
		}
		
		return (int) $value;
	}
}
?>
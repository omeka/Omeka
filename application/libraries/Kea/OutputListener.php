<?php 
/**
 * Automatically wrap any accessed properties with allhtmlentities()
 *
 * @package Omeka
 **/
require_once HELPERS;
class Kea_OutputListener extends Doctrine_EventListener
{	 
	protected $_escape;
	
	public function __construct($escape) {
		$this->_escape = $escape;
	}
	
	public function onGetProperty(Doctrine_Record $record, $property, $value)
    {
		if(!is_array($value)) {
			return allhtmlentities($value, $this->_escape);
		}else {
			return $value;
		}
    }
}
?>

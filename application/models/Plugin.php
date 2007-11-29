<?php
require_once 'Metafield.php';
/**
 * Used for plugin storage in the database
 *
 * @package default
 * 
 **/
class Plugin extends Omeka_Record
{
	public $name;
	public $active = '0';
		
	protected function _validate()
	{
		if(empty($this->name)) {
			$this->addError('name', 'Names of plugins must not be blank');
		}
	}
} // END class Location extends Omeka_Record


?>
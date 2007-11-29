<?php
require_once 'Type.php';
require_once 'Metafield.php';
/**
 * TypesMetafields join record
 *
 * @package Omeka
 * 
 **/
class TypesMetafields extends Omeka_Record
{
	public $type_id;
	public $metafield_id;
	public $plugin_id;
	
	protected function _validate()
	{
		if(empty($this->type_id)) {
			$this->addError('type_id', 'Metafield must be related to a type');
		}
		
		if(empty($this->metafield_id)) {
			$this->addError('metafield_id', 'Type must be related to a metafield');
		}
	}
} // END class TypesMetafields extends Omeka_Record

?>
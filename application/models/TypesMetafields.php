<?php
require_once 'Type.php';
require_once 'Metafield.php';
/**
 * TypesMetafields join record
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class TypesMetafields extends Kea_Record
{
	public function setUp() {
		$this->hasOne("Type", "TypesMetafields.type_id");
		$this->hasOne("Metafield", "TypesMetafields.metafield_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("type_id", "integer");
		$this->hasColumn("metafield_id", "integer");
	}
} // END class TypesMetafields extends Kea_Record

?>
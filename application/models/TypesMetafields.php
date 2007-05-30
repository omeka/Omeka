<?php
require_once 'Type.php';
require_once 'Metafield.php';
/**
 * TypesMetafields join record
 *
 * @package Omeka
 * 
 **/
class TypesMetafields extends Kea_JoinRecord
{
	protected $error_messages = array(	'type_id' => array('notnull' => 'Metafield must be related to a type'),
										'metafield_id' => array('notnull' => 'Type must be related to a metafield'));
	
	public function setUp() {
		$this->hasOne("Type", "TypesMetafields.type_id");
		$this->hasOne("Metafield", "TypesMetafields.metafield_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("type_id", "integer", null, "notnull");
		$this->hasColumn("metafield_id", "integer", null, "notnull");
		$this->index('type', array('fields' => array('type_id')));
		$this->index('metafield', array('fields' => array('metafield_id')));
	}
} // END class TypesMetafields extends Kea_Record

?>
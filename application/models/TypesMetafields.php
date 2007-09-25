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
	protected $error_messages = array(	'type_id' => array('notnull' => 'Metafield must be related to a type'),
										'metafield_id' => array('notnull' => 'Type must be related to a metafield'));
	
	public function setUp() {
		$this->hasOne("Type", "TypesMetafields.type_id");
		$this->hasOne("Metafield", "TypesMetafields.metafield_id");
		$this->hasOne("Plugin", "TypesMetafields.plugin_id");
	}
	
	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->hasColumn("type_id", "integer", null, "notnull");
		$this->hasColumn("metafield_id", "integer", null, "notnull");
		$this->hasColumn('plugin_id', 'integer');
		
		$this->index('type', array('fields' => array('type_id')));
		$this->index('metafield', array('fields' => array('metafield_id')));
		
		$this->index('type_metafield', array('fields'=>array('type_id', 'metafield_id'), 'type'=>'unique'));
	}
} // END class TypesMetafields extends Omeka_Record

?>
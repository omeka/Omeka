<?php
require_once 'Type.php';
require_once 'Plugin.php';
require_once 'Metatext.php';
require_once 'TypesMetafields.php';
require_once 'MetafieldTable.php';
require_once 'MetafieldMetatext.php';
/**
 * @package Omeka
 * 
 **/
class Metafield extends Kea_Record { 
	
	protected $error_messages = array(	'name' => array('notblank' => 'Metafield name must not be blank', 'unique' => 'Metafield name must be different than existing metafield names'));

	public function setUp() {
		//Replace with a join table
		$this->hasMany("Type as Types", "TypesMetafields.type_id");
		$this->hasOne("Plugin", "Metafield.plugin_id");
		$this->ownsMany("MetafieldMetatext as Metatext", "MetafieldMetatext.metafield_id");
		$this->ownsMany("TypesMetafields", "TypesMetafields.metafield_id");
//		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
	}

	public function setTableDefinition() {
   		$this->setTableName('metafields');
		
		$this->hasColumn("name", "string", 255, "unique|notblank");
		$this->hasColumn("description","string", null);
		$this->hasColumn("plugin_id", "integer");
 	}
}


?>
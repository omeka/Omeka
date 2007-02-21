<?php
require_once 'Type.php';
require_once 'Plugin.php';
require_once 'Metatext.php';
require_once 'TypesMetafields.php';
require_once 'MetafieldTable.php';
/**
 * @package Omeka
 * 
 **/
class Metafield extends Kea_Record { 

	public function setUp() {
		//Replace with a join table
		$this->hasMany("Type as Types", "TypesMetafields.type_id");
		$this->hasOne("Plugin", "Metafield.plugin_id");
		$this->ownsMany("Metatext", "Metatext.metafield_id");
	}

	public function setTableDefinition() {
   		$this->setTableName('metafields');
		
		$this->hasColumn("name", "string", 255, "unique");
		$this->hasColumn("description","string", null);
		$this->hasColumn("plugin_id", "integer");
 	}
}


?>
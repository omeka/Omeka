<?php
require_once 'Type.php';
/**
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Metafield extends Kea_Record { 
    public function setUp() {
		//Replace with a join table
		$this->hasOne("Type", "Metafield.type_id");
	}

	public function setTableDefinition() {
   		$this->setTableName('metafields');
		
		$this->hasColumn("name", "string", 400);
		$this->hasColumn("description","string", null);
		
		//Replace with a join table
		$this->hasColumn("type_id", "integer");
 	}
}


?>
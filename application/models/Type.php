<?php
require_once 'Metafield.php' ;
/**
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Type extends Kea_Record { 
    public function setUp() {
		//This should be hasMany but I don't feel like setting up the join table just yet
		$this->ownsMany("Metafield as Metafields", "Metafield.type_id");
	}

	public function setTableDefinition() {
   		$this->setTableName('types');
		$this->hasColumn("name","string", 200);
		$this->hasColumn("description","string", null);
 	}
}

?>
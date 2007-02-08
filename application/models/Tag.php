<?php
require_once 'Item.php' ;
require_once 'TagTable.php';
/**
 * @package Omeka
 * @author Kris Kelly
 **/
class Tag extends Kea_Record { 
    
	public function setUp() {
		$this->hasMany("Item as Items", "ItemsTags.item_id");
	}
	
	public function setTableDefinition() {
		$this->setTableName('tags');
   		$this->hasColumn("name","string", null, "unique");
 	}

	public function __toString() {
		return $this->name;
	}
}

?>
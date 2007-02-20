<?php
require_once 'Item.php';
require_once 'ItemsTags.php';
require_once 'TagTable.php';
/**
 * @package Omeka
 * 
 **/
class Tag extends Kea_Record { 
    	
	public function setUp() {
		$this->hasMany("Item as Items", "ItemsTags.item_id");
	}
	
	public function setTableDefinition() {
		$this->setTableName('tags');
   		$this->hasColumn("name","string", 255, "unique");
 	}

	public function __toString() {
		return $this->name;
	}
	
	public function getCount() {
		return count($this->ItemsTags);
	}
}

?>
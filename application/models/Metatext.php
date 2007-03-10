<?php
require_once 'Item.php';
require_once 'Metafield.php';
/**
 * @package Omeka
 * 
 **/
class Metatext extends Kea_Record { 
    public function setUp() {
		$this->hasOne("Item","Metatext.item_id");
		$this->hasOne("Metafield", "Metatext.metafield_id");
	}

	public function setTableDefinition() {
   	//	$this->setTableName('metatext');
		$this->hasColumn("item_id","integer");
		$this->hasColumn("metafield_id","integer");
		$this->hasColumn("text","string", null);
 	}

	public function __toString() {
		return $this->text;
	}
}

?>
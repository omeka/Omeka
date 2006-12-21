<?php
require_once 'Kea/Domain/Record.php';
/**
 * Item
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Item extends Kea_Domain_Record
{
	public function setUp() {
		$this->ownsOne("Collection","Item.collection_id");
		$this->ownsOne("Type","Item.type_id");
		$this->ownsOne("User","Item.user_id");
		$this->hasMany("File","File.item_id");
	}
	
	public function setTableDefinition() {
		$this->setTableName('items');
		
		$this->hasColumn("title","string",300);
		$this->hasColumn("publisher","string",300);
		$this->hasColumn("language","string",null);
		$this->hasColumn("relation","string",null);
		$this->hasColumn("coverage","string",null);
		$this->hasColumn("rights","string",null);
		$this->hasColumn("description","string");
		$this->hasColumn("source","string",null);
		$this->hasColumn("subject","string",300);
		$this->hasColumn("creator","string",300);
		$this->hasColumn("additional_creator","string",300);
		$this->hasColumn("date","date");
		$this->hasColumn("added","timestamp");
		$this->hasColumn("modified","timestamp");
		
		$this->hasColumn("type_id","integer");
		$this->hasColumn("collection_id","integer");
		$this->hasColumn("user_id","integer");

	}
} // END class Item extends Kea_Domain_Record

?>
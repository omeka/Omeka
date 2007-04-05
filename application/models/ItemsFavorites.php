<?php
/**
 * ItemsFavorites join table
 *
 * @package Omeka
 * 
 **/
class ItemsFavorites extends Kea_JoinRecord
{	
	public function setUp() {
		$this->hasOne("User", "ItemsFavorites.user_id");
		$this->hasOne("Item", "ItemsFavorites.item_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("item_id", "integer", null, "notnull");
		$this->hasColumn("user_id", "integer", null, "notnull");
		$this->hasColumn("added", "timestamp");
		
		$this->index('item', array('fields' => array('item_id')));
		$this->index('user', array('fields' => array('user_id')));
	}
	
	public function validate() {
		if(!$this->isUnique()) {
				$this->getErrorStack()->add('item_id', 'duplicate');
		}	
	}

	
} // END class ItemsTag

?>
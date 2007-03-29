<?php
require_once 'User.php';
require_once 'Item.php';
require_once 'Tag.php';
/**
 * Items_Tags join table
 *
 * @package default
 * 
 **/
class ItemsTags extends Kea_JoinRecord
{
	protected $error_messages = array('duplicate' => array('unique' => 'Tag has already been added to this item by this user'));
	
	public function setUp() {
		$this->hasOne("User", "ItemsTags.user_id");
		$this->hasOne("Item", "ItemsTags.item_id");
		$this->hasOne("Tag", "ItemsTags.tag_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("item_id", "integer", null, "notnull");
		$this->hasColumn("tag_id", "integer", null, "notnull");
		$this->hasColumn("user_id", "integer", null, "notnull");
	}
} // END class ItemsTag

?>
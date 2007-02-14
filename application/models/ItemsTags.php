<?php
require_once 'User.php';
require_once 'Item.php';
require_once 'Tag.php';
/**
 * Items_Tags join table
 *
 * @package default
 * @author Kris Kelly
 **/
class ItemsTags extends Kea_Record
{
	protected $error_messages = array('item_id' => array('duplicate' => 'Tag has already been added to this item by this user.'));
	
	public function setUp() {
		$this->hasOne("User", "ItemsTags.user_id");
		$this->hasOne("Item", "ItemsTags.item_id");
		$this->hasOne("Tag", "ItemsTags.tag_id");
	}
	
	public function setTableDefinition() {
		$this->hasColumn("item_id", "integer", null, "notnull");
		$this->hasColumn("tag_id", "integer", null, "notnull");
		$this->hasColumn("user_id", "integer");
	}
	
	public function validate() {
		$preExisting = $this->getTable()->findBySql("item_id = ? AND tag_id = ? AND user_id = ?", array($this->item_id, $this->tag_id, $this->user_id));
		if($preExisting && $it = $preExisting->getFirst()) {
			//Is there a better way to compare an object with its referent in the database?
			if($it->obtainIdentifier() != $this->obtainIdentifier()) {
				$this->getErrorStack()->add('item_id', 'duplicate');
			}
			
		}
	}

	
} // END class ItemsTag

?>
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
		$this->hasMany("User as Users", "ItemsTags.user_id");
		$this->ownsMany("ItemsTags", "ItemsTags.tag_id");
	}
	
	public function setTableDefinition() {
		$this->setTableName('tags');
   		$this->hasColumn("name","string", 255, "unique");
 	}

	public function __toString() {
		return $this->name;
	}
	
	public function tagCount() {
		if(isset($this->tagCount))
		{
			return $this->tagCount;
		}
		$q = new Doctrine_Query;
		$q->parseQuery("SELECT COUNT(it.id) as tagCount FROM ItemsTags it WHERE it.tag_id = ?");
		$res = $q->execute(array($this->id), Doctrine::FETCH_ARRAY);
		return $res[0]['i'][0];
	}
	
	public function toArray() {
		$array = parent::toArray();
		$array['tagCount'] = $this->tagCount();
		return $array;
	}
}

?>
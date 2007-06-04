<?php
require_once 'Item.php';
require_once 'ItemsTags.php';
require_once 'ExhibitsTags.php';
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
		
		$this->ownsMany("ExhibitsTags","ExhibitsTags.tag_id");
		$this->hasMany("Exhibit as Exhibits","ExhibitsTags.exhibit_id");
	}
	
	public function setTableDefinition() {
		$this->setTableName('tags');
   		$this->hasColumn("name","string", 255, "unique|notblank");
 	}

	public function __toString() {
		return $this->name;
	}
	
	public function tagCount($for="Items") {
		$q = new Doctrine_Query;
		$join = $for.'Tags';
		$q->parseQuery("SELECT COUNT(j.id) as tagCount FROM $join j WHERE j.tag_id = ?");
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
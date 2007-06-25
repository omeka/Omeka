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
		
		//Hack around the tags so that they are not auto-escaped by the OutputListener class
		$this->setListener(new Doctrine_EventListener);
		
	}
	
	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName('tags');
   		$this->hasColumn("name","string", 255, "unique|notblank");
 	}

	public function __toString() {
		return $this->name;
	}
	
	/**
	 * If a user ID is passed, then only delete the joins for that user
	 *
	 * @return bool
	 **/
	public function delete($user_id = null) {
		if(!$user_id) {
			return parent::delete();
		}
		
		$joins = $this->getTable()->getJoins();
		
		foreach ($joins as $joinTable) {
			$dql = "DELETE FROM $joinTable j WHERE j.tag_id = ? AND j.user_id = ?";
			$this->executeDql($dql, array($this->id, $user_id));
		}
	}
	
	/**
	 * Rename all the instances of a tag
	 * 1) Find a set of all the joins that need to be updated
	 * 2) Ignore the original tag if included in the list of new tags
	 * 3) Loop through the new tags, loop through the joins and create a new one for each new tag
	 * @return void
	 **/
	public function rename($newNames, $userId = null, $delimiter=",") {
		$joinClasses = $this->getTable()->getJoins();
		$joins = array();
		foreach ($joinClasses as $joinTable) {
			$dql = "SELECT j.* FROM $joinTable j WHERE j.tag_id = {$this->id}";
			if($userId) {
				$dql .= " AND j.user_id = $userId";
			}
			$joins[$joinTable] = $this->executeDql($dql);
		}
		
		if(in_array($this->name, $newNames)) {
			//Remove the original name from the list
			$newNames = array_diff($newNames,array($this->name));
			//Ignore the existing joins
			
			//If there are no new names left, finish
			if(!count($newNames)) {
				return true;
			}
			
		}else {
			//Otherwise take the first name and use it to update the existing joins
			$newName = array_shift($newNames);
		
			//Find the tag or make a new one
			$newTag = $this->getTable()->findOrNew($newName);
			$newTag->save();
			
			$newTagId = $newTag->id;
			
			//Update all the existing joins
			foreach ($joins as $joinSet) {
				foreach ($joinSet as $join) {
					$join->tag_id = $newTagId;
					
					//If saving doesn't work, its because of unique constraint violations
					//So we should delete the join because it already exists
					try {
						$join->trySave();
					} catch (Exception $e) {
						$join->delete();
					}
				}
			}
		}
		
		//Create new joins for the newly entered tags (if applicable)
		
		foreach ($newNames as $k => $newName) {
			$newTag = $this->getTable()->findOrNew($newName);
			$newTag->save();
			
			$newTagId = $newTag->id;
			
			foreach ($joins as $joinSet) {
				foreach ($joinSet as $join) {
					//clone the existing join
					$clone = $join->copy();
					
					//set the tag_id for the cloned join
					
					$clone->tag_id = $newTagId; 
					
					//save the new join
					$clone->trySave();
				}
			}
		}
					
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
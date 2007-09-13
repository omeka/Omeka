<?php
require_once 'Item.php';
require_once 'TagTable.php';
require_once 'Exhibit.php';
/**
 * @package Omeka
 * 
 **/
class Tag extends Kea_Record { 
  
	public function setUp() {
		$this->ownsMany("Taggings","Taggings.tag_id");
		
		$this->ownsMany("ItemTaggings", "ItemTaggings.tag_id");
		$this->ownsMany("CollectionTaggings", "CollectionTaggings.tag_id");
		$this->ownsMany("ExhibitTaggings", "ExhibitTaggings.tag_id");
	
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
	 * If an user ID is passed, then only delete the joins for that entity
	 *
	 * @return bool
	 **/
	public function delete($entity = null) {
		fire_plugin_hook('delete_tag', $this);
		
		$tag_id = (int) $this->id;
		
		//Delete all from taggings that have this specific tag
		$delete = "DELETE taggings, tags FROM tags 
		LEFT JOIN taggings ON taggings.tag_id = tags.id
		WHERE tags.id = $tag_id";
		
		//Delete only for a specific entity if we have passed one
		if($entity instanceof Entity) {
			$entity_id = (int) $entity->id;
			$delete .= " AND taggings.entity_id = $entity_id;";
		}		
		
		$this->execute($delete);
	}
	
	/**
	 * Rename all the instances of a tag
	 * 1) Find a set of all the joins that need to be updated
	 * 2) Ignore the original tag if included in the list of new tags
	 * 3) Loop through the new tags, loop through the joins and create a new one for each new tag
	 * @return void
	 **/
	public function rename($newNames, $user_id = null, $delimiter=",") {
		throw new Exception( 'rename taggings must be fixed' );
		$joins = array();
/*
			
		$dql = "SELECT j.* FROM Taggings j WHERE j.tag_id = ?";
			if($user_id) {
				$dql .= " AND j.user_id = $user_id";
			}
			$joins[$joinTable] = $this->executeDql($dql, array($this->id, ));
		}
		
*/	
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
}

?>
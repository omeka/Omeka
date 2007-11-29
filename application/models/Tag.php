<?php
require_once 'Item.php';
require_once 'TagTable.php';
require_once 'Exhibit.php';
/**
 * @package Omeka
 * 
 **/
class Tag extends Omeka_Record { 
  
	public $name;

	public function __toString() {
		return $this->name;
	}
	
	/**
	 * Must also delete the taggings associated with this tag
	 *
	 * @return void
	 **/
	protected function _delete()
	{
		$taggings = get_db()->getTable('Taggings')->findBySql('tag_id = ?', array((int) $this->id));
		
		foreach ($taggings as $tagging) {
			$tagging->delete();
		}
	}
	
	/**
	 * Delete only the taggings entries for this specific tag/entity combination
	 *
	 * @return void
	 **/
	protected function deleteForEntity(Entity $entity)
	{
		$taggings = get_db()->getTable('Taggings')
				->findBySql('entity_id = ? AND tag_id = ?', 
					array( (int)$entity->id, (int) $this->id));
		
		foreach ($taggings as $tagging) {
			$tagging->delete();
		}
	}
	
	protected function _validate()
	{
		if(empty($this->name)) {
			$this->addError('name', 'Tags must be given a name');
		}
		
		if(!$this->fieldIsUnique('name')) {
			$this->addError('name', 'That name is already taken for this tag');
		}
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
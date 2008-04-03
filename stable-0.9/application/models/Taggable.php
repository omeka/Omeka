<?php
/**
 * Taggable
 * Adaptation of the Rails Acts_as_taggable
 * @package: Omeka
 */
class Taggable extends Omeka_Record_Module
{	
	public function __construct(Omeka_Record $record) {

		$this->record = $record;
		
		$this->type = get_class($record);
		
		$this->tagTable = get_db()->getTable('Tag');
		
		$this->joinTable = get_db()->getTable('Taggings');
		
		$this->conn = get_db();
	}
	
	/**
	 * Fires whenever deleting a record that is taggable
	 * This will actually delete all the references to a specific tag for a specific record
	 *
	 * @return void
	 **/	
	public function beforeDelete()
	{
		$this->deleteTaggings();
	}
	
	public function deleteTaggings()
	{
		$id = (int) $this->record->id;
		
		$db = get_db();
		
		//What table should we be deleting taggings for
		$record_type = $this->type;
		$model_table = $db->$record_type;

		//Delete everything from the taggings table
		
		$delete = "DELETE $db->Taggings FROM $db->Taggings
		LEFT JOIN $model_table ON $db->Taggings.relation_id = $model_table.id
		WHERE $model_table.id = $id AND $db->Taggings.type = '$record_type'";
		
		$db->exec($delete);
	}
	
	/**
	 * Retrieve all the Taggings objects that represent between a specific tag and the current record
	 * Called by whatever record has enabled this module
	 *
	 * @return array of Taggings
	 **/
	public function getTaggings()
	{
		return $this->joinTable->findBy(array('record'=>$this->record));
	}
	
	/**
	 * Get all the Tag records associated with this record
	 *
	 * @return array of Tag
	 **/
	public function getTags()
	{
		return $this->tagTable->findBy(array('record'=>$this->record, 'return'=>'object'), $this->type);
	}

	public function entityTags($entity)
	{
		return $this->tagTable->findBy(array('entity'=>$entity, 'record'=>$this->record, 'return'=>'object'), $this->type);
	}
	
	/**
	 * Delete a tag from the record
	 *
	 * @param int user_id The user to specifically delete the tag from
	 * @param bool deleteAll Whether or not to delete all references to this tag for this record
	 * @return bool|array Whether or not the tag was deleted (false if tag doesn't exist)
	 **/
	public function deleteTags($tag, $entity=null, $deleteAll=false)
	{			
		$findWith['tag'] = $tag;
		$findWith['record'] = $this->record;
		
		//If we aren't deleting all the tags associated with a record, then find those specifically for the user
		if(!$deleteAll) {
			$findWith['entity'] = $entity;
		}
		
		$taggings = $this->joinTable->findBy($findWith);
		foreach ($taggings as $tagging) {
			$tagging->delete();
		}
	}
			
	/** If the $tag were a string and the keys of Tags were just the names of the tags, this would be:
	 * in_array(array_keys($this->Tags))
	 *
	 * @return boolean
	 **/
	public function hasTag($tag, $entity=null) {
		$count = $this->joinTable->findBy(array('tag'=>$tag, 'entity'=>$entity, 'record'=>$this->record), null, true);
		
		return $count > 0;
	}	
	
	/**
	 * Add tags for the record and for a specific entity
	 *
	 * @param array|string $tags Either an array of tags or a delimited string
	 * @param Entity $entity The entity (in record form, for which a set of tags should be added)
	 * @return void
	 **/	
	public function addTags($tags, $entity, $delimiter = ',') {
		if(!$this->record->id) {
			throw new Exception( 'A valid record ID # must be provided when tagging.' );
		}
		
		if(!$entity) {
			throw new Exception( 'A valid entity must be provided when tagging' );
		}
		
		if(!is_array($tags)) {
			$tags = explode($delimiter, $tags);
			$tags = array_diff($tags, array(''));
		}
		
		foreach ($tags as $key => $tagName) {
			$tag = $this->tagTable->findOrNew(trim($tagName));
			
			if(!$tag->exists()) {
				$tag->save();
			}
			
			$join = new Taggings;
						
			$join->tag_id = $tag->id;
			$join->relation_id = $this->record->id;
			$join->type = $this->type;
			$join->entity_id = $entity->id;
			$join->save();			
		}
	}

	/**
	 * Calculate the difference between a tag string and a set of tags
	 *
	 * @return array Keys('removed','added')
	 **/
	public function diffTagString( $string, $tags=null, $delimiter=",")
	{
		if(!$tags)
		{
			$tags = $this->record->Tags;
		}
		
		$inputTags = array();
		$existingTags = array();
		$inputTags = explode($delimiter,$string);
		
		foreach ($inputTags as $key => $inputTag) {
			$inputTags[$key] = trim($inputTag);
		}
		
		//get rid of empty tags
		$inputTags = array_diff($inputTags,array(''));
		
		foreach ($tags as $key => $tag) {
			if($tag instanceof Tag || is_array($tag)) {
				$existingTags[$key] = trim($tag["name"]);
			}else{
				$existingTags[$key] = trim($tag);
			}
			
		}
		if(!empty($existingTags)) {
			$removed = array_values(array_diff($existingTags,$inputTags));
		}
		
		if(!empty($inputTags)) {
			$added = array_values(array_diff($inputTags,$existingTags));
		}
		return compact('removed','added');
	}	

	/**
	 * This will add tags that are in the tag string and remove those that are no longer in the tag string
	 *
	 * @param string $string A string of tags delimited by $delimiter
	 * @param Entity $entity The entity that all the tags will be associated with
	 * @param bool $deleteTags When a tag is designated for removal, this specifies whether to remove all instances of the tag or just for the current Entity
	 * @return void
	 **/
	public function applyTagString($string, $entity, $deleteTags = false, $delimiter=",")
	{
		$tags = ($deleteTags) ? $this->record->Tags : $this->entityTags($entity);
		$diff = $this->diffTagString($string, $tags, $delimiter);
	
		if(!empty($diff['removed'])) {
			$this->deleteTags($diff['removed'], $entity, $deleteTags);
			
			//i.e. remove_item_tag
			$hook = 'remove_' . strtolower(get_class($this->record)) . '_tag';
			//PLUGIN HOOKS
			fire_plugin_hook($hook,  $this->record, $diff['removed'], $entity);
		}
		if(!empty($diff['added'])) {
			$this->addTags($diff['added'], $entity);
			
			//PLUGIN HOOKS
			fire_plugin_hook('add_' . strtolower(get_class($this->record)) . '_tag',  $this->record, $diff['added'], $entity);
		}
		
	}
}

?>
<?php 
/**
* Relatable strategy
*/
class Relatable extends Omeka_Record_Module
{
	protected $record;
	
	public function __construct($record)
	{
		$this->record = $record;
		$this->type = get_class($record);
	}

	/**
	 * After updating records, add a stamp that the logged-in entity has modified it
	 *
	 * @return void
	 **/
	public function afterUpdate()
	{
		if($entity = Omeka::loggedIn()->Entity) {
			$this->setModifiedBy($entity);
		}		
	}
	
	/**
	 * After inserting records, add a stamp that the logged-in entity has added it
	 *
	 * @return void
	 **/
	public function afterInsert()
	{
		if($entity = Omeka::loggedIn()->Entity) {
			$this->setAddedBy($entity);
		}		
	}
	
	public function beforeDelete()
	{
		$this->deleteRelations();
	}
	
	public function deleteRelations()
	{
		/**
		 * @duplication 
		 * @see Taggable::deleteTaggings()
		 * @since 9/13/07
		 */
		
		$id = (int) $this->record->id;
		
		//What table should we be deleting taggings for
		
		$db = get_db();
		
		//Polymorphic 'type' column in this table
		$type = (string) get_class($this->record);
		
		$model_table = $db->$type;
		
		$er = $db->EntitiesRelations;
		
		$delete = "DELETE $er FROM $er
		LEFT JOIN $model_table ON $er.relation_id = $model_table.id
		WHERE $model_table.id = $id AND $er.type = '$type'";
		
		$db->exec($delete);
	}

	/**
	 * Get the last date the item was modified, etc.
	 * @example getLastRelationship('modified') returns the date of the last modification
	 * @return void
	 **/
	public function timeOfLastRelationship($rel)
	{
		$db = get_db();
		
		$sql = "SELECT ie.time as time
				FROM {$db->EntitiesRelations} ie 
				JOIN {$db->EntityRelationships} er ON er.id = ie.relationship_id
				WHERE ie.relation_id = ? AND er.name = ? AND ie.type = ?
				ORDER BY time DESC
				LIMIT 1";
		
		$relation_id = $this->getRelationId();
				
		return $db->query($sql, array($relation_id, $rel, $this->type), true);
	}
	
	/**
	 * @example $item->getRelatedEntities('collector')
	 *
	 * @return Doctrine_Collection|array
	 **/
	public function getRelatedEntities($rel)
	{
		$db = get_db();
						
		$sql = 
		"SELECT e.* FROM {$db->Entity} e 
		INNER JOIN {$db->EntitiesRelations} r ON r.entity_id = e.id
		INNER JOIN {$db->EntityRelationships} er ON er.id = r.relationship_id
		WHERE r.relation_id = ? AND r.type = ? AND er.name = ? GROUP BY e.id";
	
		$entities = $this->getTable('Entity')->fetchObjects($sql, array($this->getRelationId(), $this->type, $rel));
		
		return !$entities ? array() : $entities;
	}
	
	/**
	 * @example $item->addRelatedTo($user, 'added')
	 *
	 * @return void
	 **/
	public function addRelatedTo($entity, $relationship )
	{		
		$entity_id = (int) ($entity instanceof Omeka_Record) ? $entity->id : $entity;		
	
		//If the entity_id is 0, die because that won't work
		if($entity_id == 0) {
			throw new Exception( 'Invalid entity provided!' );
		
			//For now, fail silently because there's no use in bitching about it
			return false;
		}
	
		$relation_id = $this->getRelationId();
		
		$relationship_id = $this->getRelationshipId($relationship);
		
		if(!$relationship_id) {
			throw new Exception( 'Relationship called '.$relationship . ' does not exist.' );
		}
		
		$db = get_db();
		
		$sql = "INSERT INTO {$db->EntitiesRelations}
					(entity_id, relation_id, relationship_id, time, `type`)
				VALUES
					(?, ?, ?, NOW(), ?)";

		return $db->exec($sql, array($entity_id, $relation_id, $relationship_id, $this->type));				
	}
	
	public function removeRelatedTo($entity, $rel, $limit = null)
	{
		$entity_id = ($entity instanceof Omeka_Record) ? $entity->id : $entity;
		
		$relation_id = $this->getRelationId();
		
		$relationship_id = $this->getRelationshipId($rel);
		
		$limit = (!empty($limit)) ? (int) $limit : null;
		
		$db = get_db();
		
		$sql = 
		"DELETE FROM {$db->EntitiesRelations}
		WHERE entity_id = ? AND relation_id = ? AND relationship_id = ? AND type = ?";
		
		if($limit) {
			$sql .= " LIMIT $limit";
		}
		
		return $db->exec($sql, array($entity_id, $relation_id, $relationship_id, $this->type));
		
	}

	protected function getRelationshipId($rel)
	{
		$db = get_db();
		$sql = "SELECT r.id FROM {$db->EntityRelationships} r WHERE r.name = ?";
		return $db->fetchOne($sql, array($rel));
	}

	protected function getRelationId()
	{
		$id =  $this->record->id;
		
		if(!$id) {
			throw new Exception( 'Record must exist before relations can be set.' );
		}
		
		return $id;
	}

	public function isRelatedTo($entity_id, $rel=null)
	{
		$conn = get_db();
		$select = new Omeka_Select;
		
		$relation_id = $this->getRelationId();
				
		$db = get_db();		
				
		$select->from("{$db->EntitiesRelations} ie", "COUNT(ie.id)")
				->innerJoin("{$db->Entity} e", "e.id = ie.entity_id")
				->where("ie.relation_id = ?", $relation_id)
				->where("ie.entity_id = ?", $entity_id)
				->where("ie.type = ?", $this->type); 
										
		if(!empty($rel)) {
			$select->innerJoin("{$db->EntityRelationships} ier", "ier.id = ie.relationship_id");
			$select->where("ier.name = ?", $rel);
		}

		$count = $select->fetchOne();
				
		return $count > 0;
	}
	
		
	public function toggleRelatedTo($entity_id, $rel) {
		
		if($this->isRelatedTo($entity_id, $rel)) {
			$this->removeRelatedTo($entity_id, $rel, 1);
		}else {
			$this->addRelatedTo($entity_id, $rel);
		}
	}	
	
	public function isRelatedToUser($user, $relationship)
	{
		if(!($user instanceof User)) {
			$entity_id = $user;
		}else {
			$entity_id = $user->entity_id;
		}
		
		if(!$this->exists()) {
			return false;
		}
		
		return $this->isRelatedTo($entity_id, $relationship);
	}
		
	public function wasAddedBy($user)
	{
		return $this->isRelatedToUser($user, 'added');
	}
	
	public function wasModifiedBy($user)
	{
		return $this->isRelatedToUser($user, 'modified');
	}
	
	public function isFavoriteOf($user) {
		$entity_id = $user->entity_id;
	
		return $this->isRelatedTo($entity_id, 'favorite');
	}
	
	public function toggleFavorite($user) {
		$entity_id = $user->entity_id;
		
		return $this->toggleRelatedTo($entity_id, 'favorite');
	}
	
	public function setAddedBy($entity) {
		return $this->addRelatedTo($entity, 'added');
	}
	
	public function setModifiedBy($entity) {		
		return $this->addRelatedTo($entity, 'modified');
	}
	
	public function addRelatedIfNotExists($entity, $rel) {
		if(!$this->isRelatedTo($entity, $rel)) {
			return $this->addRelatedTo($entity, $rel);
		}
		
		return false;
	}
}
 
?>

<?php 
/**
* Relatable strategy
*/
class Relatable	
{
	protected $record;
	
	public function __construct($record, $inheritance_id)
	{
		$this->record = $record;
		$this->relationshipsTable = $this->getTableName('EntityRelationships');
		$this->joinTable = $this->getTableName('EntitiesRelations');
		$this->entityTable = $this->getTableName('Entity');
		$this->inheritance_id = $inheritance_id;
	}
	
	public function __call($m, $a)
	{
		return call_user_func_array( array($this->record, $m), $a);
	}
	
	/**
	 * Get the last date the item was modified, etc.
	 * @example getLastRelationship('modified') returns the date of the last modification
	 * @return void
	 **/
	public function timeOfLastRelationship($rel)
	{
		$sql = "SELECT ie.time as time
				FROM {$this->joinTable} ie 
				JOIN {$this->relationshipsTable} er ON er.id = ie.relationship_id
				WHERE ie.relation_id = ? AND er.name = ? AND ie.inheritance_id = ?
				ORDER BY time DESC
				LIMIT 1";
		
		$relation_id = $this->getRelationId();
				
		return $this->execute($sql, array($relation_id, $rel, $this->inheritance_id), true);
	}
	
	/**
	 * @example $item->getRelatedEntities('collector')
	 *
	 * @return Doctrine_Collection|array
	 **/
	public function getRelatedEntities($rel)
	{
		$dql = 
		"SELECT e.* FROM Entity e 
		INNER JOIN e.EntitiesRelations r 
		INNER JOIN r.EntityRelationships er
		WHERE r.relation_id = ? AND r.inheritance_id = ? AND er.name = ?";
		
		return $this->executeDql($dql, array($this->getRelationId(), $this->inheritance_id, $rel));
	}
	
	/**
	 * @example $item->addRelatedTo($user, 'added')
	 *
	 * @return void
	 **/
	public function addRelatedTo($entity, $relationship )
	{
		$entity_id = ($entity instanceof Kea_Record) ? $entity->id : $entity;		
	
		$relation_id = $this->getRelationId();
		
		$relationship_id = $this->getRelationshipId($relationship);
		
		if(!$relationship_id) {
			throw new Exception( 'Relationship called '.$relationship . ' does not exist.' );
		}
		
		$sql = "INSERT INTO {$this->joinTable} 
					(entity_id, relation_id, relationship_id, time, inheritance_id)
				VALUES
					(?, ?, ?, NOW(), ?)";
					
		return $this->execute($sql, array($entity_id, $relation_id, $relationship_id, $this->inheritance_id));				
	}
	
	public function removeRelatedTo($entity, $rel, $limit = null)
	{
		$entity_id = ($entity instanceof Kea_Record) ? $entity->id : $entity;
		
		$relation_id = $this->getRelationId();
		
		$relationship_id = $this->getRelationshipId($rel);
		
		$limit = (!empty($limit)) ? (int) $limit : null;
		
		$sql = 
		"DELETE FROM {$this->joinTable}
		WHERE entity_id = ? AND relation_id = ? AND relationship_id = ? AND inheritance_id = ?";
		
		if($limit) {
			$sql .= " LIMIT $limit";
		}
		
		return $this->execute($sql, array($entity_id, $relation_id, $relationship_id, $this->inheritance_id));
		
	}

	protected function getRelationshipId($rel)
	{
		return $this->execute("SELECT r.id FROM {$this->relationshipsTable} r WHERE r.name = ?", array($rel), true);
	}

	protected function getRelationId()
	{
		$id =  $this->record->obtainIdentifier();
		$id = $id['id'];
		
		if(!$id) {
			throw new Exception( 'Record must exist before relations can be set.' );
		}
		
		return $id;
	}

	public function isRelatedTo($entity_id, $rel=null)
	{
		$conn = $this->getTable()->getConnection();
		$select = new Kea_Select($conn);
		
		$relation_id = $this->getRelationId();
				
		$select->from("{$this->joinTable} ie", "COUNT(ie.id)")
				->joinInner("{$this->entityTable} e", "e.id = ie.entity_id")
				->where("ie.relation_id = ?", $relation_id)
				->where("ie.entity_id = ?", $entity_id)
				->where("ie.inheritance_id = ?", $this->inheritance_id); 
										
		if(!empty($rel)) {
			$select->joinInner("{$this->relationshipsTable} ier", "ier.id = ie.relationship_id");
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
	
	public function setAddedBy($user) {
		$entity_id = $user->entity_id;
		
		return $this->addRelatedTo($entity_id, 'added');
	}
	
	public function setModifiedBy($user) {
		$entity_id = $user->entity_id;
		
		return $this->addRelatedTo($entity_id, 'modified');
	}
	
	public function addRelatedIfNotExists($entity, $rel) {
		if(!$this->isRelatedTo($entity, $rel)) {
			return $this->addRelatedTo($entity, $rel);
		}
		
		return false;
	}
}
 
?>

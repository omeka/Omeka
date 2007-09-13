<?php
require_once 'CollectionsRelations.php';
require_once 'CollectionTaggings.php';
/**
 * @package Omeka
 **/
class Collection extends Kea_Record { 

	public function construct()
	{
		require_once 'Relatable.php';
		require_once 'Entity.php';
		$this->_strategies[] = new Relatable($this);
	}

	public function setUp() {
		$this->hasMany('Item as Items', 'Item.collection_id');
		$this->ownsMany("CollectionsRelations", "CollectionsRelations.relation_id");
		$this->ownsMany("CollectionTaggings", "CollectionTaggings.relation_id");
	}
	
	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName('collections');
        $this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank'=>true));
        $this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('public', 'boolean', null, array('notnull' => true));
        $this->hasColumn('featured', 'boolean', null, array('notnull' => true));
    }
	
	
	/**
	 * @duplication
	 * @see Item::postInsert(), Item::postUpdate()
	 * @since 9/13/07
	 * @return void
	 **/
	//Make sure you set the entity relationships
	public function postInsert()
	{
		$entity = Kea::loggedIn()->Entity;
		
		$this->setAddedBy($entity);
	}
	
	//Make sure you set the entity relationships
	public function postUpdate()
	{
		$entity = Kea::loggedIn()->Entity;
		
		$this->setModifiedBy($entity);
	}
	
	public function delete()
	{
		fire_plugin_hook('delete_collection', $this);
		
		//Take care of the entities_relations DB rows
		$this->deleteRelations();
		
		$id = (int) $this->id;
		
		//Remove this collection
		$delete = "DELETE collections FROM collections WHERE id = $id";
		
		$this->execute($delete);
		
		//Reset the collection_id field for the relevant items
		$update = "UPDATE items SET collection_id = NULL WHERE collection_id = $id";
		
		$this->execute($update);
	}
	
	public function get($name) {
		switch ($name) {
			case 'added':
				return $this->timeOfLastRelationship('added');
			
			case 'modified':
				return $this->timeOfLastRelationship('modified');
			
			case 'favorite':
				$user = Kea::loggedIn();
				return $this->isFavoriteOf($user);
			
			case 'Collectors':
				return ($this->exists()) ? $this->getRelatedEntities('collector') : array();			
			default:
				return parent::get($name);
				break;
		}
	}
	
	protected function postCommitForm($post, $options)
	{
		//Process the collectors that have been provided on the form
		$collectorsPost = $post['collectors'];
		
		foreach ($collectorsPost as $k => $c) {
			if(!empty($c)) {
				//Numbers mean that an entity_id has been passed, so add the relation
				if(is_numeric($c)) {
					$entity_id = $c;
					$this->addRelatedIfNotExists($entity_id, 'collector');
				}else {
					//@todo Add support for entering a string name (this is thorny b/c of string splitting and unicode)
					throw new Exception( 'Cannot enter a collector by name.' );
				}
			}
		}
	}
	
	public function preCommitForm(&$post, $options)
	{
		//Handle the boolean vars in the form
		//This must be a radio button b/c checkboxes don't submit post correctly
		if(array_key_exists('public', $post)) {
			$this->public = (bool) $post['public'];
			unset($post['public']);
		}
			
		if(array_key_exists('featured', $post)) {
			$this->featured = (bool) $post['featured'];
			unset($post['featured']);
		}	

		return true;
	}
}

?>
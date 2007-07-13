<?php
require_once 'CollectionsRelations.php';
/**
 * @package Omeka
 **/
class Collection extends Kea_Record { 
    
	public function construct()
	{
		$this->_strategies[] = new Relatable($this, COLLECTION_RELATION_INHERITANCE_ID);
	}

	public function setUp() {
		$this->hasMany('Item as Items', 'Item.collection_id');
		$this->ownsMany("CollectionsRelations", "CollectionsRelations.relation_id");
	}
	
	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName('collections');
        $this->hasColumn('name', 'string', 255, array('notnull' => true, 'notblank'=>true));
        $this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('public', 'boolean', null, array('notnull' => true));
        $this->hasColumn('featured', 'boolean', null, array('notnull' => true));
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
				return $this->getRelatedEntities('collector');
			
			default:
				return parent::get($name);
				break;
		}
	}
}

?>
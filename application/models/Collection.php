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
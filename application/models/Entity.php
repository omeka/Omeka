<?php
require_once 'EntitiesRelations.php';
require_once 'User.php';
require_once 'Anonymous.php';
require_once 'Institution.php';
require_once 'Person.php';
/**
 * entity
 * @package: Omeka
 */
class Entity extends Kea_Record
{
	protected $error_messages = array(	'type' => array('notblank' => 'Must specify whether the name belongs to a Person or an Institution.'));
	
	protected $_pluralized = 'Entities';

	protected $_children;
	
    public function setTableDefinition()
    {
		$this->setTableName('entities');
		$this->option('type', 'MYISAM');
	
		$this->hasColumn('first_name', 'string');
		$this->hasColumn('middle_name', 'string');
		$this->hasColumn('last_name', 'string');
		$this->hasColumn('email', 'string');
		
		$this->hasColumn('institution', 'string');
		
		$this->hasColumn('parent_id', 'integer');
		
		$this->hasColumn('type', 'string', 50, array('notblank'=>true));
		
		
		$this->option('subclasses', array('Anonymous', 'Institution', 'Person'));
//		$this->index('unique', array('fields'=>array('first_name', 'last_name', 'email', 'institution'), 'type'=>'unique'));
    }

    public function setUp()
    {
		$this->ownsMany('EntitiesRelations', 'EntitiesRelations.entity_id');
		$this->ownsMany('Taggings', 'Taggings.entity_id');
		$this->hasOne('User', 'User.entity_id');
		$this->hasOne('Entity as Parent', 'Entity.parent_id');
    }

	/**
	 * These are all the things that will cause saving an entity to fault
	 *   1) saving an entity with a parent_id = id
	 *	 2) saving an entity so that the parent_id is one of its own descendants (circular relationship)
	 *
	 * @return void
	 **/
	public function validate()
	{
		if(is_numeric($this->parent_id) and ($this->parent_id == $this->id)) {
			$this->getErrorStack()->add('circular', 'An entity cannot be affiliated with itself.');
		}
		elseif($this->Parent->isDescendantOf($this)) {
			$this->getErrorStack()->add('circular', 'This entity is already affiliated with '.$this->Parent->name);
		}
		
		//Blank first and last name for a 'Person' is not OK
		if( ($this->type == 'Person') and empty($this->first_name) and empty($this->last_name)) {
			$this->getErrorStack()->add('Name', 'A name for a Person may not be completely blank');
		}
		
		//Blank institution name for an 'Institution' is not OK
		if( ($this->type == 'Institution') and empty($this->institution)) {
			$this->getErrorStack()->add('Name', 'The name of an institution may not be blank');
		}
	}

	public function set($name, $value)
	{
		if($name == 'name') {
			return $this->splitFullName($value);
		}
		else {
			return parent::set($name, $value);
		}
	}
	
	public function get($name)
	{
		if($this->hasRelation($name)) {
			$val = parent::get($name);
		}
		
		switch ($name) {
			case 'name':
				return $this->getName();
				break;
			//@remove Doctrine upgrade should take care of handling 
			case 'Children':

				if(empty($this->_children)) {
					$this->_children = $this->getChildren();
				}
				return $this->_children;
				
				break;
			case 'institution':
			
				//Pull the institution name from the parent relationship
				if(!empty($val)) {
					return $val;
				}
				
				if($this->isPerson() and !empty($this->parent_id)) {
					return $this->Parent->institution;
				}
				break;
			default:
				return $val;
				break;
		}
	}
	
	public function isPerson()
	{
		return $this->type == "Person";
	}
	
	public function isInstitution()
	{
		return $this->type == "Institution";
	}
	
	/**
	 * This should be a template method for the sub-classes that extend this (Individual, Institution)
	 *
	 * @todo On conversion to new version of doctrine, make this method a template method
	 * @return string
	 **/
	public function getName() {
		switch ($this->type) {
			case "Institution":
				return $this->institution;
				break;
			case "Person":
				return implode(' ', array($this->first_name, $this->middle_name, $this->last_name));
				break;
			case "Anonymous":
				return 'Anonymous';
				break;
			default:
				throw new Exception( 'Entity does not have a type!' );
				break;
		}
	}

	public function preSave()
	{	
		//@todo Remove this after upgrading Doctrine
		if(!empty($this->institution) and ($this->isPerson())) {
			$name = $this->institution;
			$inst = $this->getTable('Institution')->findUniqueOrNew(array('institution'=>$name));
			$inst->type = "Institution";
			$this->Parent = $inst;
			$this->Parent->save();
			$this->institution = NULL;
		}
	}

	//NESTED HIERARCHY (ADJACENCY LIST) CODE

	/**
	 * If $this's parent isn't the $entity, then check $this's parent's parent, etc. 
	 * until it's either true or the parent doesn't exist
	 *
	 * @return void
	 **/
	public function isDescendantOf($entity)
	{
		if(!$this->Parent->exists()) {
			return false;
		}
		
		if($this->Parent->id == $entity->id) {
			return true;
		}
		
		return $this->Parent->isDescendantOf($entity);
	}

	public function isAncestorOf($entity)
	{
		throw new Exception( 'Not implemented yet.' );
/*
			$c = $this->Children;
		
		if(!count($c)) {
			
		}
*/	
	}

	public function hasChildren()
	{
		return count($this->Children) > 0;
	}

	public function getChildren()
	{
		if($this->exists()) {
			$dql = "SELECT e.* FROM Entity e WHERE e.parent_id = {$this->id}";
			return $this->executeDql($dql);
		}else {
			return new Doctrine_Collection('Entity');
		}
		
	}

	//END ADJACENCY LIST CODE

	public function delete()
	{
		fire_plugin_hook('delete_entity', $this);
		
		$id = (int) $this->id;
		
		//Delete also needs to clear out the parent_id fields of the entity's children	
		$delete = "DELETE taggings, entities_relations, entities, users FROM entities
		LEFT JOIN taggings ON taggings.entity_id = entities.id
		LEFT JOIN entities_relations ON entities_relations.entity_id = entities.id
		LEFT JOIN users ON users.entity_id = entities.id
		WHERE entities.id = $id;
		UPDATE entities t SET t.parent_id = NULL WHERE t.parent_id = $id;";
				
		$this->execute($delete);		
	}
	
	
	/**
	 * This will merge $entity with $this, where $this is the record that remains in the db 
	 * (presumably as the actor it takes precedence)
	 *
	 * @return bool
	 **/
	public function merge($entity)
	{
		try {
			if(!$this->exists() or !$entity->exists()) {
				throw new Exception( 'Both of these Entities must be persistent in order to merge them.' );
			}
			
			//These are the classes where foreign keys will be affected
			$joinClasses = array('EntitiesRelations'=>'entity_id', 'User'=>'entity_id', 'Entity'=>'parent_id');			
					
			//Sql statement to update the join tables
			foreach ($joinClasses as $jc => $fk) {
				$jt = $this->getTableName($jc);
				$sql = "UPDATE $jt j SET j.$fk = $this->id WHERE j.$fk = $entity->id";
				$this->execute($sql);
			}
			$entity->delete();
			return true;
			
		} catch (Exception $e) {
			return false;
		}
	}


	public function splitFullName($name)
	{		
		throw new Exception( 'Not implemented yet' );
/*
			echo $name;
		//Remove the excess spaces via regex
		$name = preg_replace('/\s([\s\t]+)/', '', $name);
		Zend::dump( $name );
		
		$name_a = explode(' ', trim($name));
		
		switch (count($name_a)) {
			case 0:
				# code...
				break;
			
			default:
				# code...
				break;
		}
*/	
		
	}
}

?>

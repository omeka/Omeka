<?php
require_once 'EntitiesRelations.php';
require_once 'User.php';
require_once 'Anonymous.php';
require_once 'Institution.php';
require_once 'Person.php';
require_once 'EntityTable.php';
/**
 * entity
 * @package: Omeka
 */
class Entity extends Omeka_Record
{
	public $first_name;
	public $middle_name;
	public $last_name;
	public $email;
	public $institution;
	public $parent_id;
	public $type;
		
	protected $_pluralized = 'Entities';
	
	protected $_related = array(
		'name'=>'getName', 
		'institution'=>'getInstitution', 
		'Children'=>'getChildren', 
		'Parent'=>'getParent',
		'User'=>'getUser');
	
	protected function getParent()
	{
		return $this->getTable()->findBySql('id = ?', array( (int) $this->parent_id ), true);
	}
		
	/**
	 * These are all the things that will cause saving an entity to fault
	 *	 0) Not including a polymorphic type
	 *   1) saving an entity with a parent_id = id
	 *	 2) saving an entity so that the parent_id is one of its own descendants (circular relationship)
	 *	 3) blank first & last name for People
	 *	 4) blank institution name for institutions
	 *	 5) invalid email address
	 * @return void
	 **/
	protected function _validate()
	{
		if(empty($this->type)) {
			$this->addError('type', 'Must specify whether the name belongs to a Person or an Institution, etc.');
		}
		
		if(!empty($this->email) and !Zend_Validate::is($this->email, 'EmailAddress')) {
			$this->addError('email', 'The email address provided is not valid.');
		}
		
		if(is_numeric($this->parent_id)) {
			
			if($this->parent_id == $this->id) {
				$this->addError(null, 'An entity cannot be affiliated with itself.');
			}
			elseif($this->Parent->isDescendantOf($this)) {
				$this->addError(null, 'This entity is already affiliated with '.$this->Parent->name);
			}
		}
				
		//Blank first and last name for a 'Person' is not OK
		if( ($this->type == 'Person') and empty($this->first_name) and empty($this->last_name)) {
			$this->addError('Name', 'The name for a person may not be completely blank.');
		}
		
		//Blank institution name for an 'Institution' is not OK
		if( ($this->type == 'Institution') and empty($this->institution)) {
			$this->addError('Name', 'The name of an institution may not be blank.');
		}
	}

	/**
	 * Trim all the data that comes in via the form, make sure that parent_id is a valid foreign key.
	 *
	 * @return array
	 **/
	protected function filterInput($input)
	{
		$options = array('namespace'=>'Omeka_Filter');
		
		$filters = array(
			'first_name' 	=> 	'StringTrim',
			'middle_name' 	=> 	'StringTrim',
			'last_name' 	=> 	'StringTrim',
			'email'=> 'StringTrim',
			'institution'=>'StringTrim',
			'parent_id' => 'ForeignKey',
			'type'=>'Alpha');
			
		$filter = new Zend_Filter_Input($filters, null, $input, $options);

		$clean = $filter->getUnescaped();

		return $clean;
	}
	
	/**
	 * If the Entity in question is a Person, then only it's parent could be an institutio
	 *
	 * @return void
	 **/
	protected function getInstitution()
	{
		//Pull the institution name from the parent relationship
		if(!empty($this->institution)) {
			return $val;
		}
		
		if($this->isPerson() and !empty($this->parent_id)) {
			return $this->Parent->institution;
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

	//NESTED HIERARCHY (ADJACENCY LIST) CODE

	/**
	 * If $this's parent isn't the $entity, then check $this's parent's parent, etc. 
	 * until it's either true or the parent doesn't exist
	 *
	 * @return void
	 **/
	public function isDescendantOf($entity)
	{
		if(empty($this->parent_id)) {
			return false;
		}
		
		if($this->parent_id == $entity->id) {
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
	
	protected function getChildren()
	{
		if($this->exists()) {
			$db = get_db();
			
			$sql = "SELECT e.* FROM $db->Entity e WHERE e.parent_id = ?";
			$children = $this->getTable('Entity')->fetchObjects($sql, array($this->id));	
		}
		
		if(!$children) return array();
		
		return $children;
	}
	
	//END ADJACENCY LIST CODE

	public function getUser()
	{
		$id = (int) $this->id;
		return get_db()->getTable('User')->findByEntity($id);
	}
	
	/**
	 * When deleting an entity, there is much else to be done.
	 * 1) Delete any associated user account
	 * 2) Delete all taggings associated with this entity
	 * 3) Update the entities_relations table so that every reference to this entity are NULLed
	 * 4) Remove any references to this entity within the parent_id fields of all the other entities (simple update to NULL)
	 *
	 * @return void
	 **/
	public function _delete()
	{		
		$id = (int) $this->id;
		
		//Check if there is a user account associated with this
		
		if($user = $this->User) {
			$user->delete();
		}
		
		$db = get_db();
		
		//Remove all taggings associated with this entity
		$taggings = $db->getTable('Taggings')->findBy(array('entity'=>$id));
		
		foreach ($taggings as $tagging) {
			$tagging->delete();
		}
		
		//Delete also needs to clear out the parent_id fields of the entity's children	
		$update = "UPDATE $db->Entity SET parent_id = NULL WHERE parent_id = ?;";
		$update_join = "UPDATE $db->EntitiesRelations SET entity_id = NULL WHERE entity_id = ?";
		
		$db->exec($update_join, array($id));		
		$db->exec($update, array($id));
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
			
			$db = get_db();
			
			//These are the classes where foreign keys will be affected
			$joinClasses = array('EntitiesRelations'=>'entity_id', 'User'=>'entity_id', 'Entity'=>'parent_id');			
					
			//Sql statement to update the join tables
			foreach ($joinClasses as $jc => $fk) {
				$jt = $db->$jc;
				$sql = "UPDATE $jt j SET j.$fk = ? WHERE j.$fk = ?";
				$db->exec($sql, array($this->id, $entity->id));
			}
			$entity->delete();
			return true;
			
		} catch (Exception $e) {
			Zend_Debug::dump( $e );exit;
		}
	}
}

?>

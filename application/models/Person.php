<?php
require_once 'Entity.php';
require_once 'Institution.php';
/**
 * Person
 * @package: Omeka
 */
class Person extends Entity
{	
	/**
	 * Find the institution for the Entity and save it as parent_id
	 *
	 **/
	public function preSave()
	{		
		$this->type = "Person";	
		
		if(!empty($this->institution)) {
			$this->setParentToInstitution();
		}
	}
	
	protected function setParentToInstitution()
	{
		$name = $this->institution;
		
		$inst = $this->getTable('Entity')->findUniqueOrNew(array('institution'=>$name));
		$inst->type = "Institution";
		$inst->save();
		$this->parent_id = $inst->id;
		$this->institution = NULL;
	}
	
	public function getName()
	{
		return implode(' ', array($this->first_name, $this->middle_name, $this->last_name));
	}
}

?>

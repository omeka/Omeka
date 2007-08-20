<?php
require_once 'Entity.php';
require_once 'Institution.php';
/**
 * Person
 * @package: Omeka
 */
class Person extends Entity
{
	protected $_pluralized = 'People';
	
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('type'=>"Person"));
    }

	/**
	 * Find the institution for the Entity and save it as parent_id
	 *
	 **/
	public function preSave()
	{
		parent::preSave();
		
		$this->type = "Person";	
		
		if(!empty($this->institution)) {
			$this->setParentToInstitution();
			$this->institution = NULL;
		}
	}
	
	protected function setParentToInstitution()
	{
		$name = $this->institution;
		
		$inst = $this->getTable('Institution')->findUniqueOrNew(array('institution'=>$name));
		$this->Parent = $inst;
	}
	
	public function getName()
	{
		return implode(' ', array($this->first_name, $this->middle_name, $this->last_name));
	}
	
	public function get($name)
	{
		switch ($name) {
			
			case 'institution':
				if(!empty($this->parent_id)) {
					return $this->Parent->institution;
				}
				break;
			
			default:
				return parent::get($name);
				break;
		}
	}	
}

?>

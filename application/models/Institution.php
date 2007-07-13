<?php
require_once 'Entity.php';
/**
 * Institution
 * @package: Omeka
 */
class Institution extends Entity
{
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('inheritance_id'=>INSTITUTION_INHERITANCE_ID));
    }

	public function preSave()
	{
		parent::preSave();
		$this->inheritance_id = INSTITUTION_INHERITANCE_ID;
	}
	
	public function getName()
	{
		return $this->institution;
	}
}

?>
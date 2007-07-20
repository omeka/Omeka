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
		$this->setInheritanceMap(array('type'=>"Institution"));
    }

	public function preSave()
	{
		parent::preSave();
		$this->type = "Institution";
	}
	
	public function getName()
	{
		return $this->institution;
	}
}

?>
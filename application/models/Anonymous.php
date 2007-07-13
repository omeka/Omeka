<?php
require_once 'Entity.php';
/**
 * Anonymous
 * @package: Omeka
 */
class Anonymous extends Entity
{
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('inheritance_id'=>1));
    }

	public function preSave()
	{
		$this->inheritance_id = 1;
	}
	
	public function getName()
	{
		return 'Anonymous';
	}
}

?>
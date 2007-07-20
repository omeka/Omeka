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
		$this->setInheritanceMap(array('type'=>"Anonymous"));
    }

	public function preSave()
	{
		$this->type = "Anonymous";
	}
	
	public function getName()
	{
		return 'Anonymous';
	}
}

?>
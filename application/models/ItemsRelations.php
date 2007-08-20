<?php
/**
 * ItemsRelations
 * @package: Omeka
 */
class ItemsRelations extends EntitiesRelations
{
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('type'=>"Item"));
    }

	public function preSave()
	{
		$this->type = "Item";
	}
}

?>

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
		$this->setInheritanceMap(array('inheritance_id'=>ITEM_RELATION_INHERITANCE_ID));
    }

	public function preSave()
	{
		$this->inheritance_id = ITEM_RELATION_INHERITANCE_ID;
	}
}

?>
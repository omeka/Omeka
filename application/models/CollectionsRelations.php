<?php
require_once 'EntitiesRelations.php';
/**
 * CollectionsRelations
 * @package: Omeka
 */
class CollectionsRelations extends EntitiesRelations
{
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('inheritance_id'=>COLLECTION_RELATION_INHERITANCE_ID));
    }

	public function preSave()
	{
		$this->inheritance_id = COLLECTION_RELATION_INHERITANCE_ID;
	}
}

?>
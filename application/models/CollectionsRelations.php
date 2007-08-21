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
		$this->setInheritanceMap(array('type'=>'Collection'));
    }

	public function preSave()
	{
		$this->type = "Collection";
	}
}

?>
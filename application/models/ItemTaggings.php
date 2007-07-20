<?php
/**
 * ItemTaggings
 * @package: Omeka
 */
class ItemTaggings extends Taggings
{
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('type'=>"Item"));
		
		$this->hasOne("Item", "ItemTaggings.relation_id");
    }
}

?>
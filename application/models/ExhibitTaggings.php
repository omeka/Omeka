<?php
/**
 * ExhibitTaggings
 * @package: Omeka
 */
class ExhibitTaggings extends Taggings
{
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('type'=>"Exhibit"));
    }
}

?>
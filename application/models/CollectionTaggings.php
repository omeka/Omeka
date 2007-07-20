<?php
require_once 'Taggings.php';
/**
 * CollectionTaggings
 * @package: Omeka
 */
class CollectionTaggings extends Taggings
{
    public function setUp()
    {
		parent::setUp();
		$this->setInheritanceMap(array('type'=>"Collection"));
    }
}

?>
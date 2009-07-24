<?php  
require_once HELPERS;

/**
 * Tests get_collections($params, $limit)
 * in helpers/CollectionFunctions.php
 */
class Helpers_CollectionFunctions_GetCollectionsTest extends Omeka_Model_TestCase
{   

	protected function _createNewCollection($isPublic, $isFeatured, $name, $description)
	{
		$collection = new Collection;
		$collection->name = 'Test Collection '.$name;
		$collection->description = $description;
		$collection->public = $isPublic ? 1 : 0;
		$collection->featured = $isFeatured ? 1 : 0;
		$collection->save();
	}
	
	protected function _createNewCollections($numberPublic = 5, $numberPrivate = 5, $numberFeatured = 5) 
	{
		for($i=0; $i < $numberPublic; $i++) {
			$this->_createNewCollection(1, 0, 'Test Public Collection '.$i, 'Description for '.$i);
		}
		for($i=0; $i < $numberPrivate; $i++) {
			$this->_createNewCollection(0, 0, 'Test Private Collection '.$i, 'Description for '.$i);
		}
		for($i=0; $i < $numberFeatured; $i++) {
			$this->_createNewCollection(1, 1, 'Test Featured Collection '.$i, 'Description for '.$i);
		}
	}
	
    /**
     * Tests whether the get_collections helper returns data correctly from the test
     * database with no parameters.
     */

    public function testCanGetCollections() {
		$this->_createNewCollections(5,5,5);
        $collections = get_collections();
        $this->assertEquals(10, count($collections));
    }

	/**
     * Tests whether the get_collections helper returns data correctly from the test
     * database with a parameter for public set to 0.
     *
     * @internal Ticket #812 added for this test on 07/24/09.
     */
    public function testCanGetPrivateCollections() {
		$this->_createNewCollections(5,5,5);
        $collections = get_collections(array('public' => 0));
        $this->assertEquals(5, count($collections));
    }
    
    /**
     * Tests whether the get_collections helper returns data correctly from the test
     * database with a parameter for public set to 1 and featured set to 1.
     *
     * @internal Ticket #813 added for this test on 07/24/09.
     */
    public function testCanGetPublicFeaturedCollections() {
		$this->_createNewCollections(5,5,5);
        $collections = get_collections(array('public' => 1, 'featured' => 1));
        $this->assertEquals(5, count($collections));
    }
}
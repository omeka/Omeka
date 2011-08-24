<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test ItemTable class.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 */
class Omeka_Models_ItemTableTest extends PHPUnit_Framework_TestCase   
{
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter();
        $this->db = new Omeka_Db($this->dbAdapter, 'omeka_');
        Omeka_Context::getInstance()->setDb($this->db);
        $this->table = new ItemTable('Item', $this->db);
    }

	public function tearDown()
	{
		Omeka_Context::resetInstance();
	}

    public function testGetSelectAclIntegration()
    {
        // Test ItemTable::getSelect() when the ACL is not available.
        $this->assertEquals("SELECT i.* FROM omeka_items AS i", 
                            (string)$this->table->getSelect());

        // Test ItemTable::getSelect() when the ACL is available.
        $acl = new Omeka_Acl;
        $acl->add(new Zend_Acl_Resource('Items'));
        $acl->deny(null, 'Items', 'showNotPublic');
        Omeka_Context::getInstance()->setAcl($acl);
        
        $this->assertContains("WHERE (i.public = 1)", (string)$this->table->getSelect());
    }
    
    public function testSearchFilters()
    {   
		// Test public filter
		$publicSelect = $this->table->getSelect();
        $this->table->applySearchFilters($publicSelect, array('public' => true));
        $this->assertContains("(i.public = 1)", $publicSelect->getPart('where'));
        
		// Test feature filter
        $featuredSelect = $this->table->getSelect();
        $this->table->applySearchFilters($featuredSelect, array('featured' => true));
        $this->assertContains("(i.featured = 1)", $featuredSelect->getPart('where'));

		// Test recent filter
        $recentSelect = $this->table->getSelect();
        $this->table->applySearchFilters($recentSelect, array('recent' => true));
        $this->assertContains("i.id DESC", (string)$recentSelect);

		// Test type filter
		$typeSelect = $this->table->getSelect();
		$this->table->applySearchFilters($typeSelect, array('type' => '4'));
		$this->assertContains("INNER JOIN omeka_item_types AS ty ON i.item_type_id = ty.id", (string)$typeSelect);
		$this->assertContains("(ty.id = '4')", $typeSelect->getPart('where'));
		
		// Test collection filter
		$collectionSelect = $this->table->getSelect();
		$this->table->applySearchFilters($collectionSelect, array('collection' => '2'));
		$this->assertContains("INNER JOIN omeka_collections AS c ON i.collection_id = c.id", (string)$collectionSelect);
		$this->assertContains("(c.id = '2')", $collectionSelect->getPart('where'));
		
		// Test hasImage filter
        $hasDerivativeImageSelect = $this->table->getSelect();
        $this->table->applySearchFilters($hasDerivativeImageSelect, array('hasImage' => true));
        $this->assertContains("LEFT JOIN omeka_files AS f ON f.item_id = i.id", (string)$hasDerivativeImageSelect);
		$this->assertContains("f.has_derivative_image = '1'", (string)$hasDerivativeImageSelect);

		// Tests random filter
		$randomSelect = $this->table->getSelect();
		$this->table->applySearchFilters($randomSelect, array('random' => true));
		$this->assertContains("RAND()", (string)$randomSelect);
    }
	
    public function testFindRandomFeaturedDefault()
    {
        $featuredItem = $this->table->findRandomFeatured();
        $query = $this->dbAdapter->getProfiler()->getLastQueryProfile()->getQuery();
		$this->assertContains("LEFT JOIN omeka_files AS f ON f.item_id = i.id", $query);
    	$this->assertContains("(i.featured = 1)", $query);
    	$this->assertContains("(f.has_derivative_image = '1')", $query);
    	
    }
    
    public function testFindRandomFeaturedWithoutImage()
    {
        $featuredItem = $this->table->findRandomFeatured(false);
        $query = $this->dbAdapter->getProfiler()->getLastQueryProfile()->getQuery();
    	$this->assertContains("(i.featured = 1)", $query);
    }
}

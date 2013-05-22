<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test Table_Item class.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 */
class Models_Table_ItemTest extends PHPUnit_Framework_TestCase   
{
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter();
        $this->db = new Omeka_Db($this->dbAdapter, 'omeka_');

        $bootstrap = new Omeka_Test_Bootstrap;
        $bootstrap->getContainer()->db = $this->db;
        Zend_Registry::set('bootstrap', $bootstrap);
        
        $this->table = new Table_Item('Item', $this->db);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testGetSelectAclIntegration()
    {
        // Test ItemTable::getSelect() when the ACL is not available.
        $this->assertEquals("SELECT items.* FROM omeka_items AS items", 
                            (string)$this->table->getSelect());

        // Test ItemTable::getSelect() when the ACL is available.
        $acl = new Zend_Acl;
        $acl->add(new Zend_Acl_Resource('Items'));
        $acl->deny(null, 'Items', 'showNotPublic');

        Zend_Registry::get('bootstrap')->getContainer()->acl = $acl;
        
        $this->assertContains("WHERE (items.public = 1)", (string)$this->table->getSelect());
    }
    
    public function testSearchFilters()
    {   
		// Test public filter
		$publicSelect = $this->table->getSelect();
        $this->table->applySearchFilters($publicSelect, array('public' => true));
        $this->assertContains("(`items`.`public` = 1)", $publicSelect->getPart('where'));
        
		// Test feature filter
        $featuredSelect = $this->table->getSelect();
        $this->table->applySearchFilters($featuredSelect, array('featured' => true));
        $this->assertContains("(`items`.`featured` = 1)", $featuredSelect->getPart('where'));

		// Test type filter
		$typeSelect = $this->table->getSelect();
		$this->table->applySearchFilters($typeSelect, array('type' => '4'));
		$this->assertContains("INNER JOIN omeka_item_types AS item_types ON items.item_type_id = item_types.id", (string)$typeSelect);
		$this->assertContains("(item_types.id = '4')", $typeSelect->getPart('where'));
		
		// Test collection filter
		$collectionSelect = $this->table->getSelect();
		$this->table->applySearchFilters($collectionSelect, array('collection' => '2'));
		$this->assertContains("INNER JOIN omeka_collections AS collections ON items.collection_id = collections.id", (string)$collectionSelect);
		$this->assertContains("(collections.id = '2')", $collectionSelect->getPart('where'));
		
		// Test hasImage filter
        $hasDerivativeImageSelect = $this->table->getSelect();
        $this->table->applySearchFilters($hasDerivativeImageSelect, array('hasImage' => true));
        $this->assertContains("LEFT JOIN omeka_files AS files ON files.item_id = items.id", (string)$hasDerivativeImageSelect);
		$this->assertContains("files.has_derivative_image = '1'", (string)$hasDerivativeImageSelect);

		// Tests random filter
		$randomSelect = $this->table->getSelectForFindBy(array('sort_field' => 'random'));
		$this->assertContains("RAND()", (string)$randomSelect);
    }
}

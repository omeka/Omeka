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
class Models_Table_ItemTest extends Omeka_Test_TestCase
{
    public function setUpLegacy()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter();
        $this->db = new Omeka_Db($this->dbAdapter, 'omeka_');

        $bootstrap = new Omeka_Test_Bootstrap;
        $bootstrap->getContainer()->db = $this->db;
        Zend_Registry::set('bootstrap', $bootstrap);

        $this->table = new Table_Item('Item', $this->db);
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testGetSelectAclIntegration()
    {
        // Test ItemTable::getSelect() when the ACL is not available.
        $this->assertEquals("SELECT items.* FROM omeka_items AS items",
                            (string) $this->table->getSelect());

        // Test ItemTable::getSelect() when the ACL is available.
        $acl = new Zend_Acl;
        $acl->add(new Zend_Acl_Resource('Items'));
        $acl->deny(null, 'Items', 'showNotPublic');

        Zend_Registry::get('bootstrap')->getContainer()->acl = $acl;

        $this->assertStringContainsString("WHERE (items.public = 1)", (string) $this->table->getSelect());
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
        $this->assertStringContainsString("LEFT JOIN omeka_item_types AS item_types ON items.item_type_id = item_types.id", (string) $typeSelect);
        $this->assertContains("(item_types.id IN (4))", $typeSelect->getPart('where'));

        // Test type filter with a string
        $typeSelect = $this->table->getSelect();
        $this->table->applySearchFilters($typeSelect, array('type' => 'Still Image'));
        $this->assertStringContainsString("LEFT JOIN omeka_item_types AS item_types ON items.item_type_id = item_types.id", (string) $typeSelect);
        $this->assertContains("(item_types.name IN ('Still Image'))", $typeSelect->getPart('where'));

        // Test type filter with multiple strings
        $typeSelect = $this->table->getSelect();
        $this->table->applySearchFilters($typeSelect, array('type' => array('Still Image', 'Sound')));
        $this->assertStringContainsString("LEFT JOIN omeka_item_types AS item_types ON items.item_type_id = item_types.id", (string) $typeSelect);
        $this->assertContains("(item_types.name IN ('Still Image', 'Sound'))", $typeSelect->getPart('where'));

        // Test type filter with no type
        $typeSelect = $this->table->getSelect();
        $this->table->applySearchFilters($typeSelect, array('type' => 0));
        $this->assertStringNotContainsString("LEFT JOIN omeka_item_types AS item_types ON items.item_type_id = item_types.id", (string) $typeSelect);
        $this->assertContains("(items.item_type_id IS NULL)", $typeSelect->getPart('where'));

        // Test type filter with a item type object
        $type = new ItemType;
        $type->id = 6;
        $type->name = 'Foo';
        $typeSelect = $this->table->getSelect();
        $this->table->applySearchFilters($typeSelect, array('type' => $type));
        $this->assertStringContainsString("LEFT JOIN omeka_item_types AS item_types ON items.item_type_id = item_types.id", (string) $typeSelect);
        $this->assertContains("(item_types.id IN (6))", $typeSelect->getPart('where'));

        // Test type filter with multiple strings, some empty
        $typeSelect = $this->table->getSelect();
        $this->table->applySearchFilters($typeSelect, array('type' => array('Still Image', 'Sound', 0, $type)));
        $this->assertStringContainsString("LEFT JOIN omeka_item_types AS item_types ON items.item_type_id = item_types.id", (string) $typeSelect);
        $this->assertContains("(item_types.id IN (6) OR item_types.name IN ('Still Image', 'Sound') OR items.item_type_id IS NULL)", $typeSelect->getPart('where'));

        // Test collection filter
        $collectionSelect = $this->table->getSelect();
        $this->table->applySearchFilters($collectionSelect, array('collection' => '2'));
        $this->assertStringContainsString("LEFT JOIN omeka_collections AS collections ON items.collection_id = collections.id", (string) $collectionSelect);
        $this->assertContains("(collections.id IN (2))", $collectionSelect->getPart('where'));

        // Test collection filter with a collection object
        $collection = new Collection;
        $collection->id = 5;
        $collectionSelect = $this->table->getSelect();
        $this->table->applySearchFilters($collectionSelect, array('collection' => $collection));
        $this->assertStringContainsString("LEFT JOIN omeka_collections AS collections ON items.collection_id = collections.id", (string) $collectionSelect);
        $this->assertContains("(collections.id IN (5))", $collectionSelect->getPart('where'));

        // Test collection filter with multiple collections
        $collectionSelect = $this->table->getSelect();
        $this->table->applySearchFilters($collectionSelect, array('collection' => array('2', 3, $collection)));
        $this->assertStringContainsString("LEFT JOIN omeka_collections AS collections ON items.collection_id = collections.id", (string) $collectionSelect);
        $this->assertContains("(collections.id IN (2, 3, 5))", $collectionSelect->getPart('where'));

        // Test collection filter with no collection
        $collectionSelect = $this->table->getSelect();
        $this->table->applySearchFilters($collectionSelect, array('collection' => 0));
        $this->assertStringNotContainsString("LEFT JOIN omeka_collections AS collections ON items.collection_id = collections.id", (string) $collectionSelect);
        $this->assertContains("(items.collection_id IS NULL)", $collectionSelect->getPart('where'));

        // Test collection filter with a collection null
        $collectionSelect = $this->table->getSelect();
        $this->table->applySearchFilters($collectionSelect, array('collection' => null));
        $this->assertStringNotContainsString("LEFT JOIN omeka_collections AS collections ON items.collection_id = collections.id", (string) $collectionSelect);
        $this->assertNotContains("(items.collection_id IS NULL)", $collectionSelect->getPart('where'));

        // Test collection filter with multiple collections, some empty.
        $collectionSelect = $this->table->getSelect();
        $this->table->applySearchFilters($collectionSelect, array('collection' => array(null, '2', 3, 0, $collection)));
        $this->assertStringContainsString("LEFT JOIN omeka_collections AS collections ON items.collection_id = collections.id", (string) $collectionSelect);
        $this->assertContains('(collections.id IN (2, 3, 5) OR items.collection_id IS NULL)', $collectionSelect->getPart('where'));

        // Test hasImage filter
        $hasDerivativeImageSelect = $this->table->getSelect();
        $this->table->applySearchFilters($hasDerivativeImageSelect, array('hasImage' => true));
        $this->assertStringContainsString("LEFT JOIN omeka_files AS files ON files.item_id = items.id", (string) $hasDerivativeImageSelect);
        $this->assertStringContainsString("files.has_derivative_image = '1'", (string) $hasDerivativeImageSelect);

        // Tests random filter
        $randomSelect = $this->table->getSelectForFindBy(array('sort_field' => 'random'));
        $this->assertStringContainsString("RAND()", (string) $randomSelect);
    }
}

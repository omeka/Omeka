<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test Table_Collection class.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Models_Table_CollectionTest extends PHPUnit_Framework_TestCase   
{
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter();
        $this->db = new Omeka_Db($this->dbAdapter, 'omeka_');
        $bootstrap = new Omeka_Test_Bootstrap;
        $bootstrap->getContainer()->db = $this->db;
        Zend_Registry::set('bootstrap', $bootstrap);
        $this->table = new Table_Collection('Collection', $this->db);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testGetSelectAclIntegration()
    {
        // Test CollectionTable::getSelect() when the ACL is not available.
        $this->assertEquals("SELECT collections.* FROM omeka_collections AS collections", 
                            (string)$this->table->getSelect());

        // Test CollectionTable::getSelect() when the ACL is available.
        $acl = new Zend_Acl;
        $acl->add(new Zend_Acl_Resource('Collections'));
        $acl->deny(null, 'Collections', 'showNotPublic');
        Zend_Registry::get('bootstrap')->getContainer()->acl = $acl;
        
        $this->assertContains("WHERE (collections.public = 1)", (string)$this->table->getSelect());
    }
    
    public function testSearchFilters()
    {
        $publicSelect = new Omeka_Db_Select($this->dbAdapter);
        $this->table->applySearchFilters($publicSelect, array('public' => true));
        $this->assertContains("(`collections`.`public` = 1)", $publicSelect->getPart('where'));
        
        $featuredSelect = new Omeka_Db_Select($this->dbAdapter);
        $this->table->applySearchFilters($featuredSelect, array('featured' => true));
        $this->assertContains("(`collections`.`featured` = 1)", $featuredSelect->getPart('where'));
    }
    
    public function testFindRandomFeatured()
    {
        $featuredCollection = $this->table->findRandomFeatured();
        $query = $this->dbAdapter->getProfiler()->getLastQueryProfile()->getQuery();
        $this->assertContains("SELECT collections.* FROM omeka_collections AS c", $query);
        $this->assertContains("(collections.featured = 1)", $query);
        $this->assertContains("ORDER BY RAND()", $query);
    }
}

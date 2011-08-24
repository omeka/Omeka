<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test CollectionTable class.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Models_CollectionTableTest extends PHPUnit_Framework_TestCase   
{
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter();
        $this->db = new Omeka_Db($this->dbAdapter, 'omeka_');
        Omeka_Context::getInstance()->setDb($this->db);
        $this->table = new CollectionTable('Collection', $this->db);
    }

    public function testGetSelectAclIntegration()
    {
        // Test CollectionTable::getSelect() when the ACL is not available.
        $this->assertEquals("SELECT c.* FROM omeka_collections AS c", 
                            (string)$this->table->getSelect());

        // Test CollectionTable::getSelect() when the ACL is available.
        $acl = new Omeka_Acl;
        $acl->add(new Zend_Acl_Resource('Collections'));
        $acl->deny(null, 'Collections', 'showNotPublic');
        Omeka_Context::getInstance()->setAcl($acl);
        
        $this->assertContains("WHERE (c.public = 1)", (string)$this->table->getSelect());

        Omeka_Context::resetInstance();
    }
    
    public function testSearchFilters()
    {
        $publicSelect = new Zend_Db_Select($this->dbAdapter);
        $this->table->applySearchFilters($publicSelect, array('public' => true));
        $this->assertContains("(c.public = 1)", $publicSelect->getPart('where'));
        
        $featuredSelect = new Zend_Db_Select($this->dbAdapter);
        $this->table->applySearchFilters($featuredSelect, array('featured' => true));
        $this->assertContains("(c.featured = 1)", $featuredSelect->getPart('where'));
        
        $recentSelect = new Zend_Db_Select($this->dbAdapter);
        $this->table->applySearchFilters($recentSelect, array('recent' => true));
        $this->assertContains("c.id DESC", (string)$recentSelect);
    }
    
    public function testFindRandomFeatured()
    {
        $featuredCollection = $this->table->findRandomFeatured();
        $query = $this->dbAdapter->getProfiler()->getLastQueryProfile()->getQuery();
        $this->assertContains("SELECT c.* FROM omeka_collections AS c", $query);
        $this->assertContains("(c.featured = 1)", $query);
        $this->assertContains("ORDER BY RAND()", $query);
    }
}

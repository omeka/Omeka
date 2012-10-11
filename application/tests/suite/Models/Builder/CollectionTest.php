<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Models_Builder_CollectionTest extends PHPUnit_Framework_TestCase
{
    const COLLECTION_ID = 1;
    const USER_ID = 2;
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->dbAdapter->getProfiler(), $this);
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        $this->dbAdapter->appendLastInsertIdToStack(2);
        $this->builder = new Builder_Collection($this->db);
        $bootstrap = new Omeka_Test_Bootstrap();
        $bootstrap->getContainer()->db = $this->db;
        Zend_Registry::set('bootstrap', $bootstrap);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
    
    public function testBuildReturnsSavedCollection()
    {
        $this->builder->setRecordMetadata(array(
            'name' => 'foobar name'
        ));
        $collection = $this->builder->build();    
        $this->assertThat($collection, $this->isInstanceOf('Collection'));
        $this->assertTrue($collection->exists());
    }
    
    public function testCanSetValidPropertiesForCollection()
    {
        $this->builder->setRecordMetadata(array(
            'name' => 'foobar',
            'description' => 'foobar desc',
            'public' => true,
            'featured' => false,
            'owner_id' => self::USER_ID
        ));
        $collection = $this->builder->build();        
        $this->assertEquals('foobar', strip_formatting(metadata($collection, array('Dublin Core', 'Title'))));
        $this->assertEquals('foobar desc', strip_formatting(metadata($collection, array('Dublin Core', 'Description'))));
        $this->assertEquals("1", $collection->public);
        $this->assertEquals("0", $collection->featured);
        $this->assertEquals(self::USER_ID, $collection->owner_id,
            "Collection's 'owner_id' column should have been set.");
    }
    
    public function testCannotSetInvalidPropertiesForCollection()
    {
        $this->builder->setRecordMetadata(array(
            'name' => 'foobar',
            'description' => 'foobar desc',
            'public' => true,
            'featured' => false,
            'owner_id' => self::USER_ID,
            'jabberwocky' => 'huzzah'    
        ));
        $collection = $this->builder->build();
        $this->assertFalse(isset($collection->jabberwocky));
    }
}

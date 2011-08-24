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
class CollectionTest extends PHPUnit_Framework_TestCase
{
    const COLLECTION_ID = 1;
    const ENTITY_ID = 2;
    const RELATIONSHIP_ID = 3;
    const ENTITY_RELATION_ID = 4;
    const USER_ID = 5;
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->pluginBroker = new Omeka_Plugin_Broker;
        $this->collection = new Collection($this->db);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->dbAdapter->getProfiler(), $this);
    }
    
    public function testHasNoCollectors()
    {
        $this->assertFalse($this->collection->hasCollectors());
    }
    
    public function testAddCollectorByString()
    {
        $this->collection->addCollector('John Smith');
        $this->assertEquals(array('John Smith'), $this->collection->getCollectors());
    }
    
    public function testAddCollectorTrimsNameWhitespace()
    {
        $this->collection->addCollector('     John Smith        ');
        $this->assertEquals(array('John Smith'), $this->collection->getCollectors());
    }   
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddNonStringCollectorThrowsException()
    {
        $this->collection->addCollector(new Zend_Acl_Resource('whatever'));
    }
    
    public function testAddEmptyCollector()
    {   
        $this->collection->addCollector('');
        $this->assertFalse($this->collection->hasCollectors());
    }

    public function testAddWhitespaceCollector()
    {
        $this->collection->addCollector('           ');
        $this->assertFalse($this->collection->hasCollectors());
    }
    
    public function testHasSomeCollectors()
    {
        $this->collection->addCollector('John Smith');
        $this->assertTrue($this->collection->hasCollectors());
    }
    
    public function testTotalItemsGetsCountFromItemsTable()
    {
        $this->collection->totalItems();
        $this->profilerHelper->assertDbQuery("SELECT COUNT(DISTINCT(i.id)) FROM items AS i");
    }
    
    public function testTotalItems()
    {
        $this->dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(array(array(3))));        
        $this->assertEquals(3, $this->collection->totalItems());
    }
    
    public function testGetCollectorsEmpty()
    {
        $this->assertEquals(array(), $this->collection->getCollectors());
    }
    
    public function testGetCollectorsAsStringsWithoutSaving()
    {
        $this->collection->addCollector('John Smith');
        $this->collection->addCollector('Jerry Garcia');
        $this->collection->addCollector('Donald Duck');
        $this->assertEquals(array('John Smith', 'Jerry Garcia', 'Donald Duck'),
            $this->collection->getCollectors());
    }
    
    public function testSavingSerializesCollectorNames()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);        
        $this->collection->name = 'foobar';
        $this->collection->addCollector('John Smith');
        $this->collection->addCollector('Super Hans');
        $this->collection->save();
        $this->assertEquals("John Smith\nSuper Hans",
            $this->collection->collectors);
    }
    
    public function testGetCollectorsAsStringsAfterSaving()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        $this->collection->name = 'foobar';
        $this->collection->addCollector('John Smith');
        $this->collection->addCollector('Super Hans');
        $this->collection->forceSave();   
        $this->assertEquals(array('John Smith', 'Super Hans'),
            $this->collection->getCollectors());
    }
    
    public function testEmptyCollectorsStringMeansNoCollectors()
    {
        $this->collection->collectors = '';
        $this->assertFalse($this->collection->hasCollectors());
    }
    
    public function testWhitespaceCollectorsStringMeansNoCollectors()
    {
        $this->collection->collectors = '        ';
        $this->assertFalse($this->collection->hasCollectors());
    }
                    
    public function testDefaultCollectionNameNotValid()
    {
        $this->assertFalse($this->collection->isValid());
        $this->assertContains("Name: The collection name must have between", 
            (string)$this->collection->getErrors());
    }
    
    public function testCollectionNameTooLong()
    {
        $this->collection->name = str_repeat('a', 256);
        $this->assertFalse($this->collection->isValid());
        $this->assertContains("Name: The collection name must have between", 
            (string)$this->collection->getErrors());
    }
    
    public function testValidCollectionName()
    {
        $this->collection->name = str_repeat('b', 150);
        $this->assertTrue($this->collection->isValid());
    }
            
    public function testRemoveCollectorWhenHasNoCollectors()
    {
        $this->collection->id = self::COLLECTION_ID;
        $entity = new Entity($this->db);
        $entity->first_name = 'Foobar';
        $entity->last_name = 'LastName';
        $entity->id = self::ENTITY_ID;
        $this->dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createDeleteStatement(0));
        $this->dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(
            array(
                array(self::RELATIONSHIP_ID)
            )
        ));
        $this->assertFalse($this->collection->removeCollector($entity));
    }
    
    public function testInsertSetsAddedDate()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        $this->collection->name = 'foobar';
        $this->collection->save();
        $this->assertNotNull($this->collection->added);
        $this->assertThat(new Zend_Date($this->collection->added), $this->isInstanceOf('Zend_Date'),
            "'added' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }
    
    public function testInsertSetsModifiedDate()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        $this->collection->name = 'foobar';
        $this->collection->save();
        $this->assertNotNull($this->collection->modified);
        $this->assertThat(new Zend_Date($this->collection->modified), $this->isInstanceOf('Zend_Date'),
            "'modified' column should contain a valid date (signified by validity as constructor for Zend_Date)");        
    }
    
    public function testUpdateSetsModifiedDate()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        $this->collection->id = self::COLLECTION_ID;
        $this->collection->name = 'foobar';
        $this->collection->save();
        $this->assertNotNull($this->collection->modified);
        $this->assertThat(new Zend_Date($this->collection->modified), $this->isInstanceOf('Zend_Date'),
            "'modified' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testSetAddedByFailsWithNonpersistedUser()
    {
        try {
            $this->collection->setAddedBy(new User($this->db));
        } catch (Exception $e) {
            $this->assertContains("unsaved user", $e->getMessage());
            throw $e;
        }
    }
    
    public function testSetAddedByUser()
    {
        $user = new User($this->db);
        $user->id = self::USER_ID;
        $this->collection->setAddedBy($user);
        $this->assertEquals(self::USER_ID, $this->collection->owner_id);
    }    
}

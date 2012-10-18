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
class Models_CollectionTest extends PHPUnit_Framework_TestCase
{
    const COLLECTION_ID = 1;
    const USER_ID = 5;
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->pluginBroker = new Omeka_Plugin_Broker;
        $this->collection = new Collection($this->db);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->dbAdapter->getProfiler(), $this);
        $bootstrap = new Omeka_Test_Bootstrap();
        $bootstrap->getContainer()->db = $this->db;
        Zend_Registry::set('bootstrap', $bootstrap);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
    
    public function testHasNoCollectors()
    {
        // test
        $this->assertFalse($this->collection->hasCollectors());
    }
    
    public function testAddCollectorByString()
    {
        // setup
        $this->collection->addCollector('John Smith');
        
        // test
        $this->assertEquals(array('John Smith'), $this->collection->getCollectors());
    }
    
    public function testAddCollectorTrimsNameWhitespace()
    {
        // setup
        $this->collection->addCollector('     John Smith        ');
        
        // test
        $this->assertEquals(array('John Smith'), $this->collection->getCollectors());
    }   
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddNonStringCollectorThrowsException()
    {
        // test
        $this->collection->addCollector(new Zend_Acl_Resource('whatever'));
    }
    
    public function testAddEmptyCollector()
    {   
        // setup
        $this->collection->addCollector('');
        
        // test
        $this->assertFalse($this->collection->hasCollectors());
    }

    public function testAddWhitespaceCollector()
    {
        // setup
        $this->collection->addCollector('           ');
        
        // test
        $this->assertFalse($this->collection->hasCollectors());
    }
    
    public function testHasSomeCollectors()
    {
        $this->collection->addCollector('John Smith');
        
        // test
        $this->assertTrue($this->collection->hasCollectors());
    }
    
    public function testTotalItemsGetsCountFromItemsTable()
    {
        // setup
        $this->collection->totalItems();
        
        // test
        $this->profilerHelper->assertDbQuery("SELECT COUNT(DISTINCT(items.id)) FROM items");
    }
    
    public function testTotalItems()
    {
        // setup
        $this->dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(array(array(3))));        
        
        // test
        $this->assertEquals(3, $this->collection->totalItems());
    }
    
    public function testGetCollectorsEmpty()
    {
        // test
        $this->assertEquals(array(), $this->collection->getCollectors());
    }
    
    public function testGetCollectorsAsStringsWithoutSaving()
    {
        // setup
        $this->collection->addCollector('John Smith');
        $this->collection->addCollector('Jerry Garcia');
        $this->collection->addCollector('Donald Duck');
        
        // test
        $this->assertEquals(array('John Smith', 'Jerry Garcia', 'Donald Duck'), $this->collection->getCollectors());
    }
    
    public function testSavingSerializesCollectorNames()
    {
        // setup
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);        
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->addCollector('John Smith');
        $this->collection->addCollector('Super Hans');
        $this->collection->save();
        
        // test
        $this->assertEquals("John Smith\nSuper Hans", $this->collection->collectors);
    }
    
    public function testGetCollectorsAsStringsAfterSaving()
    {
        // setup
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);        
        $this->collection->addCollector('John Smith');
        $this->collection->addCollector('Super Hans');
        $this->collection->save();
        
        // test
        $this->assertEquals(array('John Smith', 'Super Hans'), $this->collection->getCollectors());
    }
    
    public function testEmptyCollectorsStringMeansNoCollectors()
    {
        // setup
        $this->collection->collectors = '';
        
        // test
        $this->assertFalse($this->collection->hasCollectors());
    }
    
    public function testWhitespaceCollectorsStringMeansNoCollectors()
    {
        // setup
        $this->collection->collectors = '        ';
        
        // test
        $this->assertFalse($this->collection->hasCollectors());
    }
    
    public function testValidCollectionName()
    {   
        // setup     
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => str_repeat('b', 150), 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        
        // test
        $this->assertTrue($this->collection->isValid());
    }
    
    public function testInsertSetsAddedDate()
    {
        // setup
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();
        
        // test
        $this->assertNotNull($this->collection->added);
        $this->assertThat(new Zend_Date($this->collection->added), $this->isInstanceOf('Zend_Date'),
            "'added' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }
    
    public function testInsertSetsModifiedDate()
    {
        // setup
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);        
        $this->collection->save();
        
        // test
        $this->assertNotNull($this->collection->modified);
        $this->assertThat(new Zend_Date($this->collection->modified), $this->isInstanceOf('Zend_Date'),
            "'modified' column should contain a valid date (signified by validity as constructor for Zend_Date)");        
    }
    
    public function testUpdateSetsModifiedDate()
    {
        // setup
        $this->dbAdapter->appendLastInsertIdToStack(self::COLLECTION_ID);
        
        $this->collection->id = self::COLLECTION_ID;
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();
        
        // test
        $this->assertNotNull($this->collection->modified);
        $this->assertThat(new Zend_Date($this->collection->modified), $this->isInstanceOf('Zend_Date'),
            "'modified' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testSetAddedByFailsWithNonpersistedUser()
    {
        // setup and test
        try {
            $this->collection->setAddedBy(new User($this->db));
        } catch (Exception $e) {            
            $this->assertContains("unsaved user", $e->getMessage());
            throw $e;
        }
    }
    
    public function testSetAddedByUser()
    {
        // setup
        $user = new User($this->db);
        $user->id = self::USER_ID;
        $this->collection->setAddedBy($user);
        
        // test
        $this->assertEquals(self::USER_ID, $this->collection->owner_id);
    }    
}

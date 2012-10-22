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
class Models_CollectionTest extends Omeka_Test_AppTestCase
{
    //const COLLECTION_ID = 1;
    const USER_ID = 5;
    
    public function setUp()
    {
        parent::setUp();
        $this->collection = new Collection($this->db);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
    
    public function testTotalItemsGetsCountFromItemsTable()
    {
        $collectionId = 1;
        
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->dbAdapter->appendLastInsertIdToStack($collectionId);
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->collection = new Collection($this->db);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->db->getAdapter()->getProfiler(), $this);
        $this->collection->totalItems();

        $this->profilerHelper->assertDbQuery("SELECT COUNT(DISTINCT(items.id)) FROM items");
    }
    
    public function testTotalItems()
    {
        $collectionId = 1;
        
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->dbAdapter->appendLastInsertIdToStack($collectionId);
        $this->db = new Omeka_Db($this->dbAdapter);
        $this->collection = new Collection($this->db);
        $this->profilerHelper = new Omeka_Test_Helper_DbProfiler($this->db->getAdapter()->getProfiler(), $this);
        
        $this->dbAdapter->appendStatementToStack(Zend_Test_DbStatement::createSelectStatement(array(array(3))));        
        
        $this->assertEquals(3, $this->collection->totalItems());
    }
    
    public function testHasContributorFalseBeforeSave()
    {
        $this->assertFalse($this->collection->hasContributor());
    }
    
    public function testHasContributorFalseAfterSave()
    {
        $this->collection->save();
        $this->assertFalse($this->collection->hasContributor());
    }
    
    public function testHasContributorTrueBeforeSave()
    {
        $elementTexts = array('Dublin Core' => array(
            'Description' => array(array('text' => '', 'html' => false)),
            'Contributor' => array(array('text' => 'Willy', 'html' => false)),
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        
        // added contributors are NOT recognized until the collection is saved.
        $this->assertFalse($this->collection->hasContributor());
    }
    
    public function testHasContributorTrueAfterSave()
    {
        $elementTexts = array('Dublin Core' => array(
            'Description' => array(array('text' => '', 'html' => false)),
            'Contributor' => array(array('text' => 'Willy', 'html' => false)),
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();
        
        $this->assertEquals('Willy', metadata($this->collection, array('Dublin Core', 'Contributor')));
        
        $this->assertTrue($this->collection->hasContributor());
    }
    
    public function testAddElementTextsByArrayBeforeSave()
    {
        $titleTextBefore = 'Jerry';
        $titleTextAfter = '';
        $creatorTextBefore = '<b>Fred</b>';
        $creatorTextAfter = '';
        $descriptionTextBefore = 'A book about Jerry';
        $descriptionTextAfter = '';
        $contributorTextBefore = '<span>Willy</span> jumped high.';
        $contributorTextAfter = '';
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => $titleTextBefore, 'html' => false)),
            'Creator' => array(array('text' => $creatorTextBefore, 'html' => true)),
            'Description' => array(array('text' => $descriptionTextBefore, 'html' => false)),
            'Contributor' => array(array('text' => $contributorTextBefore, 'html' => false)),
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        
        // element texts are NOT recognized until the collection is saved.
        $this->assertEquals($titleTextAfter, metadata($this->collection, array('Dublin Core', 'Title')));
        $this->assertEquals($creatorTextAfter, metadata($this->collection, array('Dublin Core', 'Creator')));
        $this->assertEquals($descriptionTextAfter, metadata($this->collection, array('Dublin Core', 'Description')));
        $this->assertEquals($contributorTextAfter, metadata($this->collection, array('Dublin Core', 'Contributor')));
        
    }
    
    public function testAddElementTextsByArrayAfterSave()
    {
        $titleTextBefore = 'Jerry';
        $titleTextAfter = 'Jerry';
        $creatorTextBefore = '<b>Fred</b>';
        $creatorTextAfter = '<b>Fred</b>';
        $descriptionTextBefore = 'A book about Jerry';
        $descriptionTextAfter = 'A book about Jerry';
        $contributorTextBefore = '<span>Willy</span> jumped high.';
        $contributorTextAfter = '<span>Willy</span> jumped high.';
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => $titleTextBefore, 'html' => false)),
            'Creator' => array(array('text' => $creatorTextBefore, 'html' => true)),
            'Description' => array(array('text' => $descriptionTextBefore, 'html' => false)),
            'Contributor' => array(array('text' => $contributorTextBefore, 'html' => false)),
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();
        
        // element texts are NOT recognized until the collection is saved.
        $this->assertEquals($titleTextAfter, metadata($this->collection, array('Dublin Core', 'Title')));
        $this->assertEquals($creatorTextAfter, metadata($this->collection, array('Dublin Core', 'Creator')));
        $this->assertEquals($descriptionTextAfter, metadata($this->collection, array('Dublin Core', 'Description')));
        $this->assertEquals($contributorTextAfter, metadata($this->collection, array('Dublin Core', 'Contributor')));   
    }
     
    public function testValidCollectionTitle()
    {   
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => str_repeat('b', 150), 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        
        $this->assertTrue($this->collection->isValid());
    }
    
    public function testInsertSetsAddedDate()
    {
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);
        $this->collection->save();
        
        $this->assertNotNull($this->collection->added);
        $this->assertThat(new Zend_Date($this->collection->added), $this->isInstanceOf('Zend_Date'),
            "'added' column should contain a valid date (signified by validity as constructor for Zend_Date)");
    }
    
    public function testInsertSetsModifiedDate()
    {
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);        
        $this->collection->save();
        
        $this->assertNotNull($this->collection->modified);
        $this->assertThat(new Zend_Date($this->collection->modified), $this->isInstanceOf('Zend_Date'),
            "'modified' column should contain a valid date (signified by validity as constructor for Zend_Date)");        
    }
    
    public function testUpdateSetsModifiedDate()
    {
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => 'foobar', 'html' => false)),
            'Description' => array(array('text' => '', 'html' => false))
        ));
        $this->collection->addElementTextsByArray($elementTexts);
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
        $userId = 5;
        
        $user = new User($this->db);
        $user->id = $userId;
        $this->collection->setAddedBy($user);

        $this->assertEquals($userId, $this->collection->owner_id);
    }
}
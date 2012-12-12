<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 */

class Globals_InsertCollectionTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_authenticateUser($this->_getDefaultUser()); // login as admin
    }
    
    public function testCanInsertNoMetadataCollectionWithElementTexts()
    {
        $db = $this->db;
        $isHtml = true;
        $isPublic = false;
        $isFeatured = false;
        
        $titleText = 'foobar';
        
        // Insert an collection
        $metadata = array();
        
        $elementTexts = array('Dublin Core' => array( 
            'Title'=> array(
                array('text' => $titleText, 'html' => $isHtml)
             )));
        
        $collection = insert_collection($metadata, $elementTexts);
        $this->assertInstanceOf('Collection', $collection);
                
        $fCollection = $db->getTable('Collection')->find($collection->id);
        $this->assertInstanceOf('Collection', $fCollection);
        
        $this->assertEquals($isPublic ? 1 : 0, $fCollection->public);
        $this->assertEquals($isFeatured ? 1 : 0, $fCollection->featured);
        
        $elementTexts = $fCollection->getElementTexts('Dublin Core', 'Title');
        $this->assertCount(1, $elementTexts);
        
        $titleElementText = $elementTexts[0];
        $this->assertEquals($titleText, $titleElementText->text);
        $this->assertEquals($isHtml ? 1 : 0, $titleElementText->html);

        release_object($collection);
        release_object($fCollection);
        release_object($elementTexts);
    }
    
    public function testCanInsertNoMetadataNoElementTexts()
    {
        $db = $this->db;
        $isHtml = true;
        $isPublic = false;
        $isFeatured = false;
                
        // Insert an collection
        $metadata = array();
        $elementTexts = array();
        
        $collection = insert_collection($metadata, $elementTexts);
        $this->assertInstanceOf('Collection', $collection);
                
        $fCollection = $db->getTable('Collection')->find($collection->id);
        $this->assertInstanceOf('Collection', $fCollection);
        
        $this->assertEquals($isPublic ? 1 : 0, $fCollection->public);
        $this->assertEquals($isFeatured ? 1 : 0, $fCollection->featured);
        
        $elementTexts = $fCollection->getAllElementTexts();
        $this->assertCount(0, $elementTexts);
        
        release_object($collection);
        release_object($fCollection);
        release_object($elementTexts);
    }
    
    public function testCanInsertPrivateNotFeaturedNotHtmlCollection()
    {
        $this->_testInsertCollection(false, false, false);
    }

    public function testCanInsertPrivateFeaturedNotHtmlCollection()
    {
        $this->_testInsertCollection(false, true, false);
    }
    
    public function testCanInsertPublicFeaturedNotHtmlCollection()
    {
        $this->_testInsertCollection(true, true, false);
    }
    
    public function testCanInsertPublicNotFeaturedNotHtmlCollection()
    {
        $this->_testInsertCollection(true, false, false);
    }
    
    public function testCanInsertPrivateNotFeaturedHtmlCollection()
    {
        $this->_testInsertCollection(false, false, true);
    }
    
    public function testCanInsertPrivateFeaturedHtmlCollection()
    {
        $this->_testInsertCollection(false, true, true);
    }
    
    public function testCanInsertPublicFeaturedHtmlCollection()
    {
        $this->_testInsertCollection(true, true, true);
    }
    
    public function testCanInsertPublicNotFeaturedHtmlCollection()
    {
        $this->_testInsertCollection(true, false, true);
    }
    
    /**
     * Tests the insertion of a collection with a single Dublin Core Title element text
     * 
     * @param boolean $isPublic Whether the collection is public or not
     * @param boolean $isFeatured Whether the collection is featured or not
     * @param boolean $isHtml Whether the collection has an HTML title or not
     */    
    protected function _testInsertCollection($isPublic, $isFeatured, $isHtml) 
    {
        $db = $this->db;
        
        $titleText = 'foobar';
        
        // Insert an collection
        $metadata = array('public' => $isPublic, 'featured' => $isFeatured);
        
        $elementTexts = array('Dublin Core' => array( 
            'Title'=> array(
                array('text' => $titleText, 'html' => $isHtml)
             )));
        
        $collection = insert_collection($metadata, $elementTexts);
        $this->assertInstanceOf('Collection', $collection);
                
        $fCollection = $db->getTable('Collection')->find($collection->id);
        $this->assertInstanceOf('Collection', $fCollection);
        
        $this->assertEquals($isPublic ? 1 : 0, $fCollection->public);
        $this->assertEquals($isFeatured ? 1 : 0, $fCollection->featured);
        
        $elementTexts = $fCollection->getElementTexts('Dublin Core', 'Title');
        $this->assertCount(1, $elementTexts);
        
        $titleElementText = $elementTexts[0];
        $this->assertEquals($titleText, $titleElementText->text);
        $this->assertEquals($isHtml ? 1 : 0, $titleElementText->html);

        release_object($collection);
        release_object($fCollection);
        release_object($elementTexts);
    }
}
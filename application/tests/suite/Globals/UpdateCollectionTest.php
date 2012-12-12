<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

class Globals_UpdateCollectionTest extends Omeka_Test_AppTestCase
{   
    public function setUp()
    {
       parent::setUp();
       $this->_authenticateUser($this->_getDefaultUser()); // login as admin
    }
    
    public function testCanUpdateEmptyCollection()
    {
        $oldCollection = insert_collection();
        $this->assertInstanceOf('Collection', $oldCollection);
        $this->assertTrue($oldCollection->exists());
        $this->assertEquals(0, $oldCollection->public);
        $this->assertEquals(0, $oldCollection->featured);
        $elementTexts = $oldCollection->getAllElementTexts();
        $this->assertCount(0, $elementTexts);
        
        $titleText = 'foo';
        $descriptionTextA = 'bar';
        $descriptionTextB = 'soap';
        
        $isHtml = true;
        $isPublic = true;
        $isFeatured = true;
        $metadata = array('public' => $isPublic, 'featured' => $isFeatured);
        $elementTexts = array('Dublin Core' => array( 
            'Title'=> array(
                array('text' => $titleText, 'html' => $isHtml)
             ),
             'Description'=> array(
                 array('text' => $descriptionTextA, 'html' => $isHtml),
                 array('text' => $descriptionTextB, 'html' => $isHtml)
              )
        ));        
        
        $updatedCollection = update_collection($oldCollection, $metadata, $elementTexts);
        
        $this->assertInstanceOf('Collection', $oldCollection);
        $this->assertTrue($updatedCollection->exists());
        $this->assertEquals($isPublic ? 1 : 0, $oldCollection->public);
        $this->assertEquals($isFeatured ? 1 : 0, $oldCollection->featured);
        
        $titleElementTexts = $oldCollection->getElementTexts('Dublin Core', 'Title');
        $this->assertCount(1, $titleElementTexts);
        
        $titleElementText = $titleElementTexts[0];
        $this->assertEquals($titleText, $titleElementText->text);
        $this->assertEquals($isHtml ? 1 : 0, $titleElementText->html);
        
        $descriptionElementTexts = $oldCollection->getElementTexts('Dublin Core', 'Description');
        $this->assertCount(2, $descriptionElementTexts);
        
        $descriptionElementTextA = $descriptionElementTexts[0];
        $this->assertEquals($descriptionTextA, $descriptionElementTextA->text);
        $this->assertEquals($isHtml ? 1 : 0, $descriptionElementTextA->html);
        
        $descriptionElementTextB = $descriptionElementTexts[1];
        $this->assertEquals($descriptionTextB, $descriptionElementTextB->text);
        $this->assertEquals($isHtml ? 1 : 0, $descriptionElementTextB->html);
    }
}

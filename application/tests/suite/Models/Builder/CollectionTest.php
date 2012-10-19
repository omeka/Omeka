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
class Models_Builder_CollectionTest extends Omeka_Test_AppTestCase
{
    const USER_ID = 2;
    
    public function setUp()
    {
        parent::setUp();
        $this->builder = new Builder_Collection($this->db);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
    
    public function testBuildReturnsSavedCollection()
    {
        // build collection
        $elementTexts = array(
            'Dublin Core' => array(
                'Title' => array(array('text' => 'foobar name', 'html' => false)),
            )
        );
        $this->builder->setElementTexts($elementTexts);
        $collection = $this->builder->build();
        
        $this->assertThat($collection, $this->isInstanceOf('Collection'));
        $this->assertTrue($collection->exists());
    }
    
    public function testCanSetValidPropertiesForCollection()
    {
        // build the collection
        $this->builder->setRecordMetadata(array(
            'public' => true,
            'featured' => false,
            'owner_id' => self::USER_ID
        ));
        $elementTexts = array(
            'Dublin Core' => array(
                'Title' => array(array('text' => 'foobar', 'html' => false)),
                'Description' => array(array('text' => 'foobar desc', 'html' => false)),
            )
        );
        $this->builder->setElementTexts($elementTexts);
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
            'public' => true,
            'featured' => false,
            'owner_id' => self::USER_ID,
            'jabberwocky' => 'huzzah'    
        ));
        $elementTexts = array(
            'Dublin Core' => array(
                'Title' => array(array('text' => 'foobar', 'html' => false)),
                'Description' => array(array('text' => 'foobar desc', 'html' => false)),
            )
        );
        $this->builder->setElementTexts($elementTexts);
        $collection = $this->builder->build();
        $this->assertFalse(isset($collection->jabberwocky));
    }
}
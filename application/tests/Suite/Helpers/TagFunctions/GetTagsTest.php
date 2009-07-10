<?php 
require_once HELPERS;

/**
 * Tests snippet_by_word_count($phrase, $maxWords, $ellipsis)
 * in helpers/StringFunctions.php
 */
class Helpers_TagFunctions_GetTagsTest extends Omeka_Model_TestCase
{   
    private $_defaultTagLimit;
    private $_itemToTag;
    
    public function setUp()
    {
        parent::setUp();
        $this->_defaultTagLimit = 10;
        
        $this->_itemToTag = new Item;
        $this->_itemToTag->setArray(array('title'=>'Thing'));
        $this->_itemToTag->save();
    }
    
    // adds tags to an item from the user
    // by default it adds tags to the default item from the super-user
    private function _addTags($tagStrings, $item=null, $user=null)
    {
        if (!$user) {
            $user = get_user_by_id(1);
        }
        
        if(!$item) {
            $item = $this->_itemToTag;
        }
                
        $item->addTags($tagStrings, $user);            
    }
    
    public function testGetTagsByDefaultAndWithNoTags()
    {        
        $this->assertEquals(0, count(get_tags()));
    }

    public function testGetTagsByDefaultAndWithLessThanLimitOfTags()
    {
        $this->_addTags(array('Duck', 'Chicken', 'Goose'));
        
        $tags = get_tags();
        $this->assertEquals(3, count($tags));
        $this->assertEquals('Duck', $tags[0]->name);
        $this->assertEquals('Chicken', $tags[1]->name);
        $this->assertEquals('Goose', $tags[2]->name);
    }

    public function testGetTagsByDefaultAndWithMoreThanLimitOfTags()
    {
        $tags = array();        
        for($i = 0; $i < $this->_defaultTagLimit + 10; $i++) {
            $tags[] = (string)$i;
        }
                    
        $this->_addTags($tags);

        $tags = get_tags();
        $this->assertEquals($this->_defaultTagLimit, count($tags));
        for($i = 0; $i < $this->_defaultTagLimit; $i++) {
            $this->assertEquals((string)$i, $tags[$i]->name);            
        }
    }

    public function testGetTagsWithNoLimitAndWithMoreThanDefaultLimitOfTags()
    {
        $tags = array();        
        for($i = 0; $i < $this->_defaultTagLimit + 10; $i++) {
            $tags[] = (string)$i;
        }        
        $this->_addTags($tags);     

        $tags = get_tags(array(), 0);
        $this->assertEquals($this->_defaultTagLimit + 10, count($tags));
        for($i = 0; $i < $this->_defaultTagLimit; $i++) {
            $this->assertEquals((string)$i, $tags[$i]->name);            
        }
    }

    public function tearDown()
    {        
        parent::tearDown();
    }
}
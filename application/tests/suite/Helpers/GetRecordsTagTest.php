<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests get_records('Tag').
 *
 * @package Omeka
 */
class Omeka_Helpers_GetRecordsTagTest extends Omeka_Test_AppTestCase
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
    private function _addTags($tagStrings, $item=null)
    {
        if(!$item) {
            $item = $this->_itemToTag;
        }
                
        $item->addTags($tagStrings);
        $item->save();
    }
    
    public function testGetTagsByDefaultAndWithNoTags()
    {        
        $this->assertEquals(0, count(get_records('Tag')));
    }

    public function testGetTagsByDefaultAndWithLessThanLimitOfTags()
    {
        $this->_addTags(array('Duck', 'Chicken', 'Goose'));
        
        $tags = get_records('Tag');
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

        $tags = get_records('Tag');
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

        $tags = get_records('Tag', array(), 0);
        $this->assertEquals($this->_defaultTagLimit + 10, count($tags));
        for($i = 0; $i < $this->_defaultTagLimit; $i++) {
            $this->assertEquals((string)$i, $tags[$i]->name);            
        }
    }

    public function testGetTagsOnPublicItem()
    {
        /**
         * Create a new public item, with three tags.
         */
        $item = new Item;
        $item->public = 1;
        $item->save();
        $this->_addTags(array('Duck', 'Chicken', 'Goose'), $item);

        /**
         * Get tags for type=Item and public=true. Should return 3 tags, since
         * our item is public.
         */
        $publicTags = get_records('Tag', array('public' => true, 'type' => 'Item'));
        $this->assertEquals(3, count($publicTags));

        /**
         * Get tags for type=Item and public=false. Should return 0 tags, since
         * our item is public.
         */
        $nonPublicTags = get_records('Tag', array('public' => false, 'type' => 'Item'));
        $this->assertEquals(0, count($nonPublicTags));
    }

    public function testGetTagsOnNonPublicItem()
    {
        $this->_addTags(array('Duck', 'Chicken', 'Goose'));

        // Should return 0 tags, since our item is not public.
        $publicTags = get_records('Tag', array('public' => true, 'type' => 'Item'));
        $this->assertEquals(0, count($publicTags));

        /**
         * Get tags for type=Item and public=false. Should return 0 tags, since
         * our item is not public and we're not logged in.
         */
        $nonPublicTags = get_records('Tag', array('public' => false, 'type' => 'Item'));
        $this->assertEquals(0, count($publicTags));

        /**
         * Get tags for type=Item and public=false, with an authenticated user.
         * Should return 3 tags, since our item is not public and we are logged
         * in.
         */
        $this->_authenticateUser($this->_getDefaultUser());
        $nonPublicTagsAuth = get_records('Tag', array('public' => false, 'type' => 'Item'));
        $this->assertEquals(3, count($nonPublicTagsAuth));
    }
}

<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * Tests get_tags() in helpers/TagFunctions.php.
 *
 * @package Omeka
 */
class Helpers_TagFunctions_GetTagsTest extends Omeka_Test_AppTestCase
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
        $publicTags = get_tags(array('public' => true, 'type' => 'Item'));
        $this->assertEquals(3, count($publicTags));

        /**
         * Get tags for type=Item and public=false. Should return 0 tags, since
         * our item is public.
         */
        $nonPublicTags = get_tags(array('public' => false, 'type' => 'Item'));
        $this->assertEquals(0, count($nonPublicTags));
    }

    public function testGetTagsOnNonPublicItem()
    {
        $this->_addTags(array('Duck', 'Chicken', 'Goose'));

        // Should return 0 tags, since our item is not public.
        $publicTags = get_tags(array('public' => true, 'type' => 'Item'));
        $this->assertEquals(0, count($publicTags));

        /**
         * Get tags for type=Item and public=false. Should return 0 tags, since
         * our item is not public and we're not logged in.
         */
        $nonPublicTags = get_tags(array('public' => false, 'type' => 'Item'));
        $this->assertEquals(0, count($publicTags));

        /**
         * Get tags for type=Item and public=false, with an authenticated user.
         * Should return 3 tags, since our item is not public and we are logged
         * in.
         */
        $this->_authenticateUser($this->_getDefaultUser());
        $nonPublicTagsAuth = get_tags(array('public' => false, 'type' => 'Item'));
        $this->assertEquals(3, count($nonPublicTagsAuth));
    }
}

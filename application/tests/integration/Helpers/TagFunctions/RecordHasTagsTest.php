<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * Tests
 *
 * @package Omeka
 * @subpackage Tests
 */
class Omeka_Helpers_RecordHasTagsTest extends Omeka_Test_AppTestCase
{
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testItemHasTags()
    {
        // Add an item without tags, and check if record_has_tags() returns false. 
        $item = new Item;
        $item->save();
        $this->assertFalse(record_has_tags($item));

        // Add tags to the item, and check if record_has tags() returns true.
        $item->addTags(array('foo','bar'));
        $item->save();
        $this->assertTrue(record_has_tags($item));
    }

    /**
     * Checks whether a model that does not have the hasTags() method returns 
     * false for record_has_tags(). If Omeka ever does decide to use tags on 
     * Users, this test will have to use another model, or create one of its 
     * own.
     */
    public function testUserHasTags()
    {
      $user = new User;
      $this->assertFalse(record_has_tags($user));
    }
}

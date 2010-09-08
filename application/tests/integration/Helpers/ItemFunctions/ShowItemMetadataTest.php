<?php
/**
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test class for show_item_metadata
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 */
class Omeka_Helper_ShowItemMetadataTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();

        // A dispatch is currently necessary because show_item_metadata
        // calls out to a view script, and the paths are only set at
        // preDispatch.
        $this->dispatch();
    }

    /**
     * Tests that show_item_metadata behaves the same when an item is
     * set on the view and when it is directly passed.
     */
    public function testWithNoItemOnView()
    {
        $item = new Item;
        $item->item_type_id = 1;

        $metadataOutput = show_item_metadata(array(), $item);

        __v()->item = $item;

        // Compare runs with and without item set on view, they should be
        // the same.
        $this->assertEquals($metadataOutput, show_item_metadata());
    }
}

<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test class for show_item_metadata
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Helper_ShowItemMetadataTest extends Omeka_Test_AppTestCase
{
    /**
     * Tests that show_item_metadata can correctly produce an array as output.
     */
    public function testArrayOutput()
    {
        $title = 'title';
        $subject = 'subject';
        $description = 'description';

        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => $title, 'html' => false)),
            'Subject' => array(array('text' => $subject, 'html' => false)),
            'Description' => array(array('text' => $description, 'html' => false))
        ));

        $item = new Item;
        $item->addElementTextsByArray($elementTexts);
        $item->save();

        $metadataOutput = show_item_metadata(array('return_type' => 'array'), $item);

        $this->assertInternalType('array', $metadataOutput);
        $this->assertArrayHasKey('Dublin Core', $metadataOutput);

        $this->assertEquals($title, $metadataOutput['Dublin Core']['Title'][0]);
        $this->assertEquals($subject, $metadataOutput['Dublin Core']['Subject'][0]);
        $this->assertEquals($description, $metadataOutput['Dublin Core']['Description'][0]);
    }

    /**
     * Tests that show_item_metadata behaves the same when an item is
     * set on the view and when it is directly passed.
     */
    public function testWithNoItemOnView()
    {
        $item = new Item;
        $item->item_type_id = 1;

        $metadataOutput = show_item_metadata(array('return_type' => 'array'), $item);

        set_current_item($item);

        // Compare runs with and without item set on view, they should be
        // the same.
        $this->assertEquals($metadataOutput, show_item_metadata(array('return_type' => 'array')));
    }
}

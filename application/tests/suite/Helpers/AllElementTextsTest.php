<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test class for all_element_texts
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Helpers_AllElementTextsTest extends Omeka_Test_AppTestCase
{
    /**
     * Tests that all_element_texts can correctly produce an array as output.
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

        $metadataOutput = all_element_texts($item, array('return_type' => 'array'));

        $this->assertInternalType('array', $metadataOutput);
        $this->assertArrayHasKey('Dublin Core', $metadataOutput);

        $this->assertEquals($title, $metadataOutput['Dublin Core']['Title'][0]);
        $this->assertEquals($subject, $metadataOutput['Dublin Core']['Subject'][0]);
        $this->assertEquals($description, $metadataOutput['Dublin Core']['Description'][0]);
    }

    /**
     * Tests that all_element_texts behaves the same when an item is
     * set on the view and when it is directly passed.
     */
    public function testWithNoItemOnView()
    {
        $item = new Item;
        $item->item_type_id = 1;

        $metadataOutput = all_element_texts($item, array('return_type' => 'array'));

        set_current_record('item', $item, true);

        // Compare runs with and without item set on view, they should be
        // the same.
        $this->assertEquals($metadataOutput, all_element_texts('item', array('return_type' => 'array')));
    }
}

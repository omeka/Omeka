<?php 
class Tickets_759Test extends Omeka_Test_AppTestCase
{
    public function testInsertItemTypeAndInsertElementSetHaveSimilarArguments()
    {
        // Insert an item type.
        $itemType = insert_item_type(
            array('name' => 'Foobar', 'description' => 'Changed description.'),
            array(
                array('name' => 'Wonder'),
                array('name' => 'Years')
            ));

        $elementSet = insert_element_set(
            array('name' => 'Foobar Element Set', 'description' => 'foobar'),
            array(
                array('name' => 'Element Name', 'description' => 'Element Description')
            )
        );
    }
}

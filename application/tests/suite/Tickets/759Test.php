<?php 
class Tickets_759Test extends Omeka_Test_AppTestCase
{
    public function testInsertItemTypeAndInsertElementSetHaveSimilarArguments()
    {
        // Insert an item type.
        $itemType = insert_item_type(
            ['name' => 'Foobar', 'description' => 'Changed description.'],
            [
                ['name' => 'Wonder'],
                ['name' => 'Years']
            ]);

        $elementSet = insert_element_set(
            ['name' => 'Foobar Element Set', 'description' => 'foobar'],
            [
                ['name' => 'Element Name', 'description' => 'Element Description']
            ]
        );

        $db = get_db();

        $this->assertInstanceOf('ItemType', $db->getTable('ItemType')->findByName('Foobar'));
        $this->assertInstanceOf('Element', $db->getTable('Element')->findByElementSetNameAndElementName('Foobar Element Set', 'Element Name'));
    }
}

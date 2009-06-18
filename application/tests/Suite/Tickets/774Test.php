<?php

/**
* 
*/
class Tickets_774Test extends Omeka_Model_TestCase
{
    public function testItemFindByCanRetrieveNonPublicItems()
    {
        $publicItem = insert_item(array('public'=>true));
        $nonPublicItem = insert_item(array('public'=>false));
        
        // This should only retrieve 1 item.
        $items = get_db()->getTable('Item')->findBy(array('public'=>false));
        $this->assertEquals(1, count($items), '2 items were retrieved instead of the expected 1.');
    }
}
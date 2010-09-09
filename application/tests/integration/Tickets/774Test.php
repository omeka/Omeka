<?php

/**
* 
*/
class Tickets_774Test extends Omeka_Test_AppTestCase
{    
    private $_dbHelper;
    
    public function setUp()
    {
        parent::setUp();
        $this->_dbHelper = Omeka_Test_Helper_Db::factory($this->core);
        
        $acl = $this->core->getBootstrap()->getResource('Acl');
        $acl->allow(null, 'Items', 'showNotPublic');
    }
    
    public function testItemFindByCanRetrieveNonPublicItems()
    {
        $publicItem = insert_item(array('public'=>true));
        $nonPublicItem = insert_item(array('public'=>false));
        
        // This should only retrieve 1 item.
        $items = get_db()->getTable('Item')->findBy(array('public'=>false));
        $this->assertEquals(1, count($items), count($items) . ' items were retrieved instead of the expected 1.');
    }
}
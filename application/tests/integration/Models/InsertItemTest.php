<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Models_InsertItemTest extends Omeka_Model_TestCase
{   
    public function testCanInsertItem()
    {
        $this->_assertTableIsEmpty('omeka_items');
        $this->_assertTableIsEmpty('omeka_element_texts');
        
        // Insert an item and verify with a second query.
        $item = insert_item(
            array('public'=>true), 
            array('Dublin Core'=>array('Title'=>array(array('text'=>'foobar', 'html'=>true)))));
        $sql = "SELECT id, public FROM omeka_items";
        $row = $this->getAdapter()->fetchRow($sql);
        $this->assertEquals(array('id'=>1, 'public'=>1), $row);
        
        // Verify that element texts are inserted correctly into the database.
        $sql = "SELECT COUNT(id) FROM omeka_element_texts WHERE html = 1 AND text = 'foobar'";
        $this->assertEquals(1, $this->getAdapter()->fetchOne($sql));
    }
}

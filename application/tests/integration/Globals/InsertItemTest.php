<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 */

class Globals_InsertItemTest extends Omeka_Test_AppTestCase
{   
    public function testCanInsertItem()
    {
        $db = $this->db;
        
        // Insert an item and verify with a second query.
        $item = insert_item(
            array('public'=>true), 
            array('Dublin Core'=>array('Title'=>array(array('text'=>'foobar', 'html'=>true)))));
        $sql = "SELECT public FROM $db->Item WHERE id = {$item->id}";
        $row = $db->fetchRow($sql);
        $this->assertEquals(array('public' => 1), $row);
        
        // Verify that element texts are inserted correctly into the database.
        $sql = "SELECT COUNT(id) FROM $db->ElementText WHERE html = 1 AND "
            . "text = 'foobar' AND record_id = {$item->id}";
        $this->assertEquals(1, $db->fetchOne($sql));
        release_object($item);
    }
}

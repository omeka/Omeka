<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Globals_InsertItemTest extends Omeka_Test_AppTestCase
{   
    private $_dbHelper;

    public function setUp()
    {
       parent::setUp();
       $this->_dbHelper = Omeka_Test_Helper_Db::factory($this->core);
    }
    
    public function testCanInsertItem()
    {
        $db = $this->core->getBootstrap()->db;
        
        $this->assertEquals(0, $this->_dbHelper->getRowCount($db->Item));
        $this->assertEquals(0, $this->_dbHelper->getRowCount($db->ElementText));
        
        // Insert an item and verify with a second query.
        $item = insert_item(
            array('public'=>true), 
            array('Dublin Core'=>array('Title'=>array(array('text'=>'foobar', 'html'=>true)))));
        $sql = "SELECT id, public FROM $db->Item";
        $row = $this->_dbHelper->fetchRow($sql);
        $this->assertEquals(array('id'=>1, 'public'=>1), $row);
        
        // Verify that element texts are inserted correctly into the database.
        $sql = "SELECT COUNT(id) FROM $db->ElementText WHERE html = 1 AND text = 'foobar'";
        $this->assertEquals(1, $this->_dbHelper->fetchOne($sql));
    }
}

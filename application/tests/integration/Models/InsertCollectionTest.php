<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Models_InsertCollectionTest extends Omeka_Test_AppTestCase
{
    private $_dbHelper;
    
    public function setUp()
    {
        parent::setUp();
        $this->_dbHelper = Omeka_Test_DbHelper::factory($this->core);
    }
    
    public function testCanInsertCollection()
    {
        // Verify no collections exist.
        $this->assertEquals(0, $this->_dbHelper->getRowCount('omeka_collections'));
        
        // Insert a collection and verify with a second query.
        $collection = insert_collection(array('name'=>'Foo Bar', 'public'=>true, 'description'=>'foo'));
        $sql = "SELECT id, public FROM omeka_collections";
        $row = $this->_dbHelper->fetchRow($sql);
        $this->assertEquals(array('id'=>1, 'public'=>1), $row);
    }
}

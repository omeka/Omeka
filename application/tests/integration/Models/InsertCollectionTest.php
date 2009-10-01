<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Test
 **/

class Models_InsertCollectionTest extends Omeka_Model_TestCase
{
    public function testCanInsertCollection()
    {
        // Verify no collections exist.
        $this->_assertTableIsEmpty('omeka_collections');
        
        // Insert a collection and verify with a second query.
        $collection = insert_collection(array('name'=>'Foo Bar', 'public'=>true, 'description'=>'foo'));
        $sql = "SELECT id, public FROM omeka_collections";
        $row = $this->getAdapter()->fetchRow($sql);
        $this->assertEquals(array('id'=>1, 'public'=>1), $row);
    }
}

<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test class for get_items() helper.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Helper_GetItemsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Link a mock item table to a mock database.
        $this->db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $this->itemTable = $this->getMock('ItemTable', array(), array(), '', false);
        $this->db->expects($this->any())
                 ->method('getTable')
                 ->with('Item')
                 ->will($this->returnValue($this->itemTable));         
                 
        Omeka_Context::getInstance()->setDb($this->db);         
    }
    
    public function testDelegatesToItemTable()
    {
        $params = array('foobar' => true);
        $limit = 5;
        $item = new Item;
        $this->itemTable->expects($this->once())
                 ->method('findBy')
                 ->with($params, $limit)
                 ->will($this->returnValue(array($item)));
        $this->assertEquals(array($item), get_items($params, $limit));             
    }
}

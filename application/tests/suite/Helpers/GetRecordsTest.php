<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests get_records() in helpers/Functions.php
 *
 * Should just test that it delegates properly to CollectionsTable::findBy(),
 * which should be tested separately.
 * 
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */ 
class Helpers_GetRecordsTest extends PHPUnit_Framework_TestCase
{   
    public function setUp()
    {
        // Link a mock collections table to a mock database.
        $this->db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $this->collectionTable = $this->getMock('Table_Collection', array(), array(), '', false);
        $this->db->expects($this->any())
                 ->method('getTable')
                 ->with('Collection')
                 ->will($this->returnValue($this->collectionTable));

        $bootstrap = new Omeka_Test_Bootstrap;
        $bootstrap->getContainer()->db = $this->db;
        Zend_Registry::set('bootstrap', $bootstrap);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
    
    public function testDelegatesToCollectionTable()
    {
        $params = array('foobar' => true);
        $limit = 5;
        $collection = new Collection;
        $this->collectionTable->expects($this->once())
                 ->method('findBy')
                 ->with($params, $limit)
                 ->will($this->returnValue(array($collection)));
        $this->assertEquals(array($collection), get_records('Collection', $params, $limit));             
    }
}

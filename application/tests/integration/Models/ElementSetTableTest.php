<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test ElementSetTable.
 *
 * @package Omeka
 */
class Omeka_Models_ElementSetTableTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->table = $this->db->getTable('ElementSet');
    }

    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testFindByRecordType()
    {
        $this->assertEquals(2, count($this->table->findByRecordType('Item')));
        $this->assertEquals(0, count($this->table->findByRecordType('Foo', false)));
    }

    public function testFindForItems()
    {
        $this->assertEquals(2, count($this->table->findForItems()));
    }

    public function testFindByName()
    {
        $this->assertEquals(1, count($this->table->findByName('Dublin Core')));
        $this->assertEquals(1, count($this->table->findByName('Item Type Metadata')));
    }
}
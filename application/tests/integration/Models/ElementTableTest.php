<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test ElementTable.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Models_ElementTableTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->table = $this->db->getTable('Element');
    }

    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testFindByElementSetNameAndElementName()
    {
        $this->assertNull($this->table->findByElementSetNameAndElementName('Foo', 'Bar'));
        $this->assertEquals(1, count($this->table->findByElementSetNameAndElementName('Dublin Core', 'Title')));
    }

    public function testFindByElementSetNameAndElementNameCaseSensitive()
    {
        $this->assertEquals(0, count($this->table->findByElementSetNameAndElementName('Dublin Core', 'title')),
            "Should have been no results returned for lowercase 'title' element.");
        $this->assertEquals(0, count($this->table->findByElementSetNameAndElementName('dublin Core', 'Title')),
            "Should have been no results returned for lowercase 'dublin Core' element set.");
    }
}

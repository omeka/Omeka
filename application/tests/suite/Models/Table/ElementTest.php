<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test Table_Element.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Models_Table_ElementTest extends Omeka_Test_AppTestCase
{
    public function setUpLegacy()
    {
        parent::setUpLegacy();
        $this->table = $this->db->getTable('Element');
    }

    public function testFindByElementSetNameAndElementName()
    {
        $this->assertNull($this->table->findByElementSetNameAndElementName('Foo', 'Bar'));
        $this->assertNotNull($this->table->findByElementSetNameAndElementName('Dublin Core', 'Title'));
    }

    public function testFindByElementSetNameAndElementNameCaseSensitive()
    {
        $this->assertNull($this->table->findByElementSetNameAndElementName('Dublin Core', 'title'),
            "Should have been no results returned for lowercase 'title' element.");
        $this->assertNull($this->table->findByElementSetNameAndElementName('dublin Core', 'Title'),
            "Should have been no results returned for lowercase 'dublin Core' element set.");
    }
}

<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Require view helper functions.
 */
 
/**
 * Tests link_to_item()
 * in helpers/LinkFunctions.php
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */ 
class Omeka_Helper_LinkFunctions_LinkToItemsAtomTest extends Omeka_Test_AppTestCase
{
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testLinkToItemsAtomDefault()
    {
        $linkHtml = '<a class="atom" href="/items/browse?output=atom">Atom</a>';
        $this->assertEquals($linkHtml, link_to_items_atom());
	}

    public function testLinkToItemsAtomWithParameters()
    {
        $linkHtml = '<a class="atom" href="/items/browse?collection=1&featured=1&type=3&output=atom">Atom</a>';
        $this->assertEquals($linkHtml, link_to_items_atom('Atom', array('collection' => '1', 'featured' => 1, 'type' => '3')));
	}
}

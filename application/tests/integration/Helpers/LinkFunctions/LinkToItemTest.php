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
class Omeka_Helper_LinkFunctions_LinkToItemTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->item = $this->db->getTable('Item')->find(1);
    }

    public function testLinkToItemWithCurrentItem()
    {
        set_current_item($this->item);
        $linkToItemHtml = '<a href="/items/show/1">' . Installer_Test::TEST_ITEM_TITLE 
            . '</a>';
        $this->assertEquals($linkToItemHtml, link_to_item());
        
    }
    
    public function testLinkToItemWithNullLinkText()
    {
        $linkToItemHtml = '<a href="/items/show/1">' . Installer_Test::TEST_ITEM_TITLE 
            . '</a>';
        $this->assertEquals($linkToItemHtml, link_to_item(null, array(), 'show', $this->item));
    }
    
}

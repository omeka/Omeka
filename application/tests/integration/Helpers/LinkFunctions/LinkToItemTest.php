<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
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
 * @copyright Center for History and New Media, 2007-2010
 */ 
class Omeka_Helper_LinkFunctions_LinkToItemTest extends Omeka_Test_AppTestCase
{
    public function testLinkToItemWithCurrentItem()
    {
        $title = 'title';
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => $title, 'html' => false))
        ));
        
        $item = new Item;
        $item->addElementTextsByArray($elementTexts);
        $item->save();
        
        set_current_item($item);
        
        $linkToItemHtml = '<a href="/items/show/1">title</a>';
        $this->assertEquals($linkToItemHtml, link_to_item());
        
    }
    
    public function testLinkToItemWithNullLinkText()
    {
        $title = 'title';
        
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => $title, 'html' => false))
        ));
        
        $item = new Item;
        $item->addElementTextsByArray($elementTexts);
        $item->save();
        
        $linkToItemHtml = '<a href="/items/show/1">title</a>';
        $this->assertEquals($linkToItemHtml, link_to_item(null, array(), 'show', $item));
        
    }
    
}
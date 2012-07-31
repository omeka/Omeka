<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Tests item_citation()
 * in helpers/ItemFunctions.php
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 */ 
class Omeka_Helper_ItemCitationTest extends Omeka_Test_AppTestCase
{
    /**
     * Test the default output of item_citation(), which should include 
     * DC:Creator, DC:Title, site title, date accessed, and the absolute item 
     * URI.
     */
    public function testDefaultOutput()
    {
        $citationHtml = $this->_createItemCitationString('Item Title', 'Item Creator');
        $this->assertEquals($citationHtml, item_citation());
    }
    
    public function testOutputWithoutCreator()
    {
        $citationHtml = $this->_createItemCitationString('Item Title');
        $this->assertEquals($citationHtml, item_citation());
    }
    
    /**
     * Creates a test Item and returns a citation string.
     *
     * @todo This function, like item_citation(), uses date() to generate a date
     * accessed for the citation. This should be changed when we
     * internationalize date outputs.
     *
     * @param string|null The Item title.
     * @param string|null The Item creator.
     * @return string The Item's citation string.
     */
    protected function _createItemCitationString($title = null, $creator = null)
    {
        $elementTexts = array('Dublin Core' => array(
            'Title' => array(array('text' => $title, 'html' => false)),
            'Creator' => array(array('text' => $creator, 'html' => false))
        ));
        
        $item = new Item;
        $item->addElementTextsByArray($elementTexts);
        $item->save();
        
        set_current_item($item);
        
        $siteTitle = settings('site_title');
        $dateAccessed = date('F j, Y');
        $itemUrl = abs_item_uri();
        
        $citationHtml = "&#8220;$title,&#8221; <em>$siteTitle</em>, accessed $dateAccessed, $itemUrl.";
        
        if ($creator) {
            $citationHtml = "$creator, $citationHtml";
        }
        
        return $citationHtml;
    }
}

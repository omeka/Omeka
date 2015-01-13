<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * Tests Item::getCitation()
 * in helpers/ItemFunctions.php
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 */ 
class Omeka_Helper_ItemCitationTest extends Omeka_Test_AppTestCase
{
    /**
     * Test the default output of Item::getCitation(), which should include 
     * DC:Creator, DC:Title, site title, date accessed, and the absolute item 
     * URI.
     */
    public function testDefaultOutput()
    {
        $citationHtml = $this->_createItemCitationString('Item Title', 'Item Creator');
        $this->assertEquals($citationHtml, metadata('item', 'citation', array('no_escape' => true)));
    }
    
    public function testOutputWithoutCreator()
    {
        $citationHtml = $this->_createItemCitationString('Item Title');
        $this->assertEquals($citationHtml, metadata('item', 'citation', array('no_escape' => true)));
    }
    
    /**
     * Creates a test Item and returns a citation string.
     *
     * @todo This function, like Item::getCitation(), uses date() to generate a date
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
        
        set_current_record('item', $item, true);
        
        $siteTitle = option('site_title');
        $dateAccessed = format_date(time(), Zend_Date::DATE_LONG);
        $itemUrl = record_url('item', null, true);
        
        $citationHtml = "&#8220;$title,&#8221; <em>$siteTitle</em>, accessed $dateAccessed, <span class=\"citation-url\">$itemUrl</span>.";
        
        if ($creator) {
            $citationHtml = "$creator, $citationHtml";
        }
        
        return $citationHtml;
    }
}

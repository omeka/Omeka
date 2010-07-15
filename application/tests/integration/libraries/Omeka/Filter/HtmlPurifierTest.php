<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Filter_HtmlPurifierTest extends Omeka_Test_AppTestCase
{
    public function assertPreConditions()
    {
        $this->assertEquals(get_option('html_purifier_is_enabled'), '1');
        $this->assertEquals(get_option('html_purifier_allowed_html_elements'), implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertEquals(get_option('html_purifier_allowed_html_attributes'), implode(',', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
    }
    
    protected function _getHtmlPurifierPlugin($allowedHtmlElements=null, $allowedHtmlAttributes=null)
    {
        $htmlPurifier =  $this->_getHtmlPurifier($allowedHtmlElements, $allowedHtmlAttributes);
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier();
        return $htmlPurifierPlugin;
    }
    
    protected function _getHtmlPurifier($allowedHtmlElements=null, $allowedHtmlAttributes=null)
    {                   
        $htmlPurifier = Omeka_Filter_HtmlPurifier::createHtmlPurifier($allowedHtmlElements, $allowedHtmlAttributes);
        Omeka_Filter_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        return $htmlPurifier;
    }

    public function testEmptyConstructorWithValidSettings()
    {        
        $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
    }

    public function testEmptyConstructorWithInvalidSettingsAllowedHtmlAttributesThatLackAssociatedAllowedHtmlElements()
    {
        $dirtyHtml = 'whatever';
        $cleanHtml = 'whatever';
        
        set_option('html_purifier_allowed_html_elements','');
        set_option('html_purifier_allowed_html_attributes', 'a.href');        
        $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        $this->assertEquals($cleanHtml, $htmlPurifierFilter->filter($dirtyHtml));
    }
    
    public function testGetHtmlPurifier()
    {
        $htmlPurifier = $this->_getHtmlPurifier();
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());
    
        $htmlPurifier = $this->_getHtmlPurifier(array('p','strong'));
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());
    
        $htmlPurifier = $this->_getHtmlPurifier(null,array('*.class'));
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());
    
        $htmlPurifier = $this->_getHtmlPurifier(array('p','strong'),array('*.class'));
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());
    }
    
    public function testSetHtmlPurifier()
    {
        $htmlPurifier = $this->_getHtmlPurifier();
        Omeka_Filter_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());
        $this->assertEquals($htmlPurifier, Zend_Registry::get('html_purifier'));
    
        $htmlPurifier = $this->_getHtmlPurifier(array('p','strong'));
        Omeka_Filter_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());
        $this->assertEquals($htmlPurifier, Zend_Registry::get('html_purifier'));
    
        $htmlPurifier = $this->_getHtmlPurifier(null,array('*.class'));
        Omeka_Filter_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());
        $this->assertEquals($htmlPurifier, Zend_Registry::get('html_purifier'));
    
        $htmlPurifier = $this->_getHtmlPurifier(array('p','strong'),array('*.class'));
        Omeka_Filter_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        $this->assertEquals($htmlPurifier, Omeka_Filter_HtmlPurifier::getHtmlPurifier());        
        $this->assertEquals($htmlPurifier, Zend_Registry::get('html_purifier'));
    }
    
    public function testFilterAllowedAndUnallowedElements()
    {
        $this->assertTrue(in_array('p', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertFalse(in_array('code', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        
        $dirtyHtml = '<p>Bob is <code>bad</code></p>';
        $cleanHtml = '<p>Bob is bad</p>';
        
        $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        $filteredHtml = $htmlPurifierFilter->filter($dirtyHtml);
        $this->assertEquals($cleanHtml, $filteredHtml);
    }
    
    public function testFilterAllowedAndUnallowedAttributes()
    {
        $this->assertTrue(in_array('p', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertFalse(in_array('code', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertTrue(in_array('*.class', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
        $this->assertFalse(in_array('*.id', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlAttributes()));
        
        $dirtyHtml = '<p id="bob" class="person">Bob is <code class="emotion">bad</code></p>';
        $cleanHtml = '<p class="person">Bob is bad</p>';
        
        $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        $filteredHtml = $htmlPurifierFilter->filter($dirtyHtml);
        $this->assertEquals($cleanHtml, $filteredHtml);
    }
    
    public function testFilterUnallowedScriptElement()
    {
        $this->assertTrue(in_array('p', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        $this->assertFalse(in_array('script', Omeka_Filter_HtmlPurifier::getDefaultAllowedHtmlElements()));
        
        $dirtyHtml = '<p>Bob is <script>bad</script></p>';
        $cleanHtml = '<p>Bob is </p>';
        
        $htmlPurifierFilter = new Omeka_Filter_HtmlPurifier();
        $filteredHtml = $htmlPurifierFilter->filter($dirtyHtml);
        $this->assertEquals($cleanHtml, $filteredHtml);
    }
    
    public function testFilterAttributesWithMissingElements()
    {
        $htmlElements=array();
        $dirtyHtmlAttributes = array('strong.id', 'div.class', '*.class','p.id', 'a.href');
        $cleanHtmlAttributes = array();
        $this->assertEquals($cleanHtmlAttributes, Omeka_Filter_HtmlPurifier::filterAttributesWithMissingElements($dirtyHtmlAttributes, $htmlElements));        
    
        $htmlElements=array('h1');
        $dirtyHtmlAttributes = array('strong.id', 'div.class', '*.class','p.id', 'a.href');
        $cleanHtmlAttributes = array('*.class');
        $this->assertEquals($cleanHtmlAttributes, Omeka_Filter_HtmlPurifier::filterAttributesWithMissingElements($dirtyHtmlAttributes, $htmlElements));
    
        $htmlElements= array('p','strong');
        $dirtyHtmlAttributes = array('strong.id', 'div.class', '*.class','p.id', 'a.href');
        $cleanHtmlAttributes = array('strong.id', '*.class', 'p.id');
        $this->assertEquals($cleanHtmlAttributes, Omeka_Filter_HtmlPurifier::filterAttributesWithMissingElements($dirtyHtmlAttributes, $htmlElements));
    }
}
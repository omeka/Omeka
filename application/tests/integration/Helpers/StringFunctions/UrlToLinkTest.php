<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Tests url_to_link() helper in application/helpers/StringFunctions.php
 *
 * @package Omeka
 **/
class Helpers_StringFunctions_UrlToLinkTest extends Omeka_Test_AppTestCase
{
    public function testUrl()
    {
        $urlStr = "http://example.com";
        $linkStr = "<a href=\"http://example.com\">http://example.com</a>";
        $this->assertEquals($linkStr, url_to_link($urlStr));
    }
    
    public function testUrlInSentence()
    {
        $urlStr = "Lorem ipsum http://example.com dolor.";
        $linkStr = "Lorem ipsum <a href=\"http://example.com\">http://example.com</a> dolor.";
        $this->assertEquals($linkStr, url_to_link($urlStr));
    }
    
    public function testUrlAtEndOfSentence()
    {
        $urlStr = "Lorem ipsum http://example.com";
        $linkStr = "Lorem ipsum <a href=\"http://example.com\">http://example.com</a>";
        $this->assertEquals($linkStr, url_to_link($urlStr));
    }
    
    public function testUrlAtStartOfSentence()
    {
        $urlStr = "http://example.com lorem ipsum dolor";
        $linkStr = "<a href=\"http://example.com\">http://example.com</a> lorem ipsum dolor";
        $this->assertEquals($linkStr, url_to_link($urlStr));
    }
    
    public function testUrlInStringWithNoSpaces()
    {
        $urlStr = "loremipsumhttp://example.comdolorsitamet";
        $this->assertEquals($urlStr, url_to_link($urlStr));
    }
    
    public function testUrlToLinkWithQueryString()
    {
        $urlStr = "http://example.com?lorem=ipsum&dolor=sit";
        $linkStr = "<a href=\"http://example.com?lorem=ipsum&amp;dolor=sit\">http://example.com?lorem=ipsum&dolor=sit</a>";
        $this->assertEquals($linkStr, url_to_link($urlStr));
    }
    
}
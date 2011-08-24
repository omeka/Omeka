<?php 
require_once HELPERS;

class SnippetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->reporting = error_reporting();
        error_reporting(E_ALL);
    }
    
    public function testSnippetEmptyText()
    {
        $this->assertEquals('', snippet('', 0, 15));
    }
    
    public function testSnippetLongerThanText()
    {
        $this->assertEquals('text', snippet('text', 0, 15));
    }
    
    public function testSnippetShorterThanFirstWord()
    {
        $this->assertEquals('…', snippet('text', 0, 2));
    }
    
    public function testSnippetWithSpaces()
    {
        $this->assertEquals('this is some text', snippet('this is some text', 0, 250));
    }
    
    public function testShortSnippetBreaksOnSpaces()
    {
        $this->assertEquals('this is…', snippet('this is some text', 0, 10));
    }
    
    public function testSnippetWithAlternateAppend()
    {
        $this->assertEquals('this is...', snippet('this is some text', 0, 10, '...'));
    }
    
    public function testSnippetWithNoAppend()
    {
        $this->assertEquals('this is', snippet('this is some text', 0, 10, ''));
    }
    
    public function testSnippetWithHTMLInText()
    {
        $this->assertEquals('this is', snippet('<a href="url">this is some text</a>', 0, 10, ''));
    }
    
    public function testSnippetWithNoHTMLCloseTagInText()
    {
        $this->assertEquals('this is', snippet('<a href="url">this is some text', 0, 10, ''));
    }
    
    public function tearDown()
    {
        error_reporting($this->reporting);
    }
}

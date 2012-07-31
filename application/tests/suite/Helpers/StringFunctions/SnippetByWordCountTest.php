<?php 
require_once HELPERS;

/**
 * Tests snippet_by_word_count($phrase, $maxWords, $ellipsis)
 * in helpers/StringFunctions.php
 */
class Helpers_StringFunctions_SnippetByWordCountTest extends PHPUnit_Framework_TestCase
{   
    public function setUp()
    {
        $this->reporting = error_reporting();
        error_reporting(E_ALL);
    }

    public function testSnippetByWordCountEmptyText()
    {
        $phrase = '';
        $wordCount = 3;
        $targetPhrase = '';
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount));
    }

    public function testSnippetByWordCountZeroWords()
    {
        $phrase = 'All bulls eat grass.';
        $wordCount = 0;
        $targetPhrase = '';
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount));
    }

    public function testSnippetByWordCountWithNoMaxWords()
    {
        $phrase = 'All worms eat grass, except for those who were born in January, March, April, May, June, July, August, September, and October.';
        $ellipsis = '...';
        $targetPhrase = 'All worms eat grass, except for those who were born in January, March, April, May, June, July, August, September, and' . $ellipsis;
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase));
    }

    public function testSnippetByWordCountWithMaxWordsHigherThanWordCount()
    {
        $phrase = 'All cows eat grass.';
        $wordCount = 8;
        $targetPhrase = 'All cows eat grass.';
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount));
    }

    public function testSnippetByWordCountWithMaxWordsLowerThanWordCount()
    {
        $phrase = 'All birds eat grass.';
        $wordCount = 2;
        $ellipsis = '...';
        $targetPhrase = 'All birds' . $ellipsis;
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount, $ellipsis));
    }
    
    public function testSnippetByWordCountWithMaxWordsEqualsWordCount()
    {
        $phrase = 'All squirrels eat grass.';
        $wordCount = 4;
        $targetPhrase = 'All squirrels eat grass.';
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount));
    }

    public function testSnippetByWordCountWithDashes()
    {
        $phrase = 'All man-apes eat grass.';
        $wordCount = 3;
        $ellipsis = '...';
        $targetPhrase = 'All man-apes eat' . $ellipsis;
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount));
    }

    public function testShortSnippetByDefaultEllipsis()
    {
        $phrase = 'All deer eat grass.';
        $wordCount = 2;
        $ellipsis = '...';
        $targetPhrase = 'All deer' . $ellipsis;
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount));
    }

    public function testSnippetByWordCountWithAlternateEllipsis()
    {
        $phrase = 'All zebras eat grass.';
        $wordCount = 3;
        $ellipsis = ' ...';
        $targetPhrase = 'All zebras eat' . $ellipsis;
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount, $ellipsis));
    }

    public function testSnippetByWordCountWithNoEllipsis()
    {
        $phrase = 'All goats eat grass.';
        $wordCount = 2;
        $ellipsis = '';
        $targetPhrase = 'All goats';
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount, $ellipsis));
    }

    public function testSnippetWithWordCountWithHTMLInText()
    {
        $phrase = '<a href="url">All goats eat grass.</a>';
        $wordCount = 2;
        $ellipsis = '';
        $targetPhrase = 'All goats';
        $this->assertEquals($targetPhrase, snippet_by_word_count($phrase, $wordCount, $ellipsis));
    }

    public function tearDown()
    {
        error_reporting($this->reporting);
    }
}

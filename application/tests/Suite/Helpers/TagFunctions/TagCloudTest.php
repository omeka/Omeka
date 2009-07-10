<?php 
require_once HELPERS;

/**
 * Tests snippet_by_word_count($phrase, $maxWords, $ellipsis)
 * in helpers/StringFunctions.php
 */
class Helpers_TagFunctions_TagCloudTest extends Omeka_Model_TestCase
{       
    public function setUp()
    {
        parent::setUp();
    }

    public function testEmptyTagCloud()
    {
        $this->assertEquals('<p>No tags are available.</p>', tag_cloud());
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
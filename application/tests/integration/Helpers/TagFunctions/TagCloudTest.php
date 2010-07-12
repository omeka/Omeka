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
require_once HELPERS;

/**
 * Tests snippet_by_word_count($phrase, $maxWords, $ellipsis)
 * in helpers/StringFunctions.php
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */ 
class Omeka_Helper_TagFunctions_TagCloudTest extends PHPUnit_Framework_TestCase
{       
    public function testEmptyTagCloud()
    {
        $this->assertEquals('<p>No tags are available.</p>', tag_cloud());
    }
}
<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Require view helper functions.
 */
require_once HELPERS;

/**
 * Tests tag_cloud() in application/helpers/TagFunctions.php.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */ 
class Omeka_Helper_TagFunctions_TagCloudTest extends PHPUnit_Framework_TestCase
{       
    public function testEmptyTagCloud()
    {
        $this->assertEquals('<p>No tags are available.</p>', tag_cloud());
    }
}

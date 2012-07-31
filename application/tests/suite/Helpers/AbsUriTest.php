<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Helper_AbsUriTest extends Omeka_Test_AppTestCase
{
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testAbsUriDoesNotGiveNoticeIfMissingHttpHost()
    {
        try {
            $uri = abs_uri();
        } catch (PHPUnit_Framework_Error_Notice $e) {
            $this->assertNotContains("Undefined index:  HTTP_HOST", $e->getMessage(),
                "abs_uri() should not give an 'Undefined index:  HTTP_HOST' notice.");
        }
    }
    
    public function testAbsUriUsesHttpHost()
    {   
        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $uri = abs_uri(array('controller' => 'items', 'action' => 'browse'), 'default');
        $this->assertEquals('http://www.example.com/items/browse', $uri);
    }
}

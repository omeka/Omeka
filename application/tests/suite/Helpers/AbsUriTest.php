<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Helper_AbsUriTest extends Omeka_Test_AppTestCase
{
    public function testAbsUriUsesHttpHost()
    {
        $_SERVER['HTTP_HOST'] = 'www.example.com';
        $uri = absolute_url(['controller' => 'items', 'action' => 'browse'], 'default');
        $this->assertEquals('http://www.example.com/items/browse', $uri);
    }
}

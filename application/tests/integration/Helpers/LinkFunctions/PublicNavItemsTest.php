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
 * Tests public_nav_items()
 * in helpers/LinkFunctions.php
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */ 
class Omeka_Helper_LinkFunctions_PublicNavItemsTest extends PHPUnit_Framework_TestCase
{
    public function testWithEmptyArray()
    {
        $links = array();
        
        $navHtml = '';
        
        $this->dispatch('/items/browse');
        $this->assertEquals($navHtml, public_nav_items($links, null));
    }
    
    public function testWithSimpleArray()
    {
        $links = array('All Items' => '/items/browse', 'Tags' => '/items/tags');
        
        $navHtml = '';
        $navHtml .= '<li class="nav-all-items current"><a href="/items/browse">All Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-tags"><a href="/items/tags">Tags</a></li>' . "\n";

        $this->dispatch('/items/browse');
        
        $this->assertEquals($navHtml, public_nav_items($links));        
    }
    
    private function dispatch($url)
    {
        $request = new Zend_Controller_Request_HttpTestCase;
        $request->setRequestUri($url);
        Zend_Controller_Front::getInstance()->setRequest($request);
    }
}

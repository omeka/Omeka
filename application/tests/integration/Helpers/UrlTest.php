<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPER_DIR . '/Url.php';

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_View_Helper_UrlTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->front = Zend_Controller_Front::getInstance();
        $this->front->getRouter()->addDefaultRoutes();
        $this->helper = new Omeka_View_Helper_Url();
    }
    
    public function testStringUrlWithQueryParameters()
    {
        $url = $this->helper->url('items/browse', array('param1' => 'foo', 'param2' => 'bar'));
        $this->assertEquals("/items/browse?param1=foo&param2=bar", $url);
    }
    
    public function testArrayUrlWithDefaultRouteAndQueryParameters()
    {
        $url = $this->helper->url(array('controller'=>'items', 
                                        'action'=>'browse'),
                                  null,
                                  array('param1' => 'foo',
                                        'param2' => 'bar'));
        
        $this->assertEquals("/items/browse?param1=foo&param2=bar", $url);                              
    }
    
    public function testStringUrlWithSingleStartingSlash()
    {
        $url = $this->helper->url('/items/browse');
        $this->assertEquals("/items/browse", $url);
    }
    
    public function testStringUrlWithMultipleStartingSlashes()
    {
        $url = $this->helper->url('/////items/browse');
        $this->assertEquals("/items/browse", $url);
    }
    
    public function tearDown()
    {
        $this->front->resetInstance();
    }
}

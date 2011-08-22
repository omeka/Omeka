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
 * Tests nav($links)
 * in helpers/LinkFunctions.php
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */ 
class Omeka_Helper_LinkFunctions_NavTest extends Omeka_Test_AppTestCase
{
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    public function testEmptyLinkArray()
    {
        $this->assertEquals('', nav(array()));
    }
    
    public function testSimpleLinkArray()
    {
        $links = array('Browse Items' => '/items', 'Browse Collections' => '/collections', 'Home' => '/');
        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a></li>' . "\n";
        $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
        $this->assertEquals($navHtml, nav($links));
    }
    
    public function testWithoutSomeUrls()
    {
        $links = array('Browse Items' => '/items', 'Browse Collections' => '', 'Home' => '/');
        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections">Browse Collections</li>' . "\n";
        $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
        $this->assertEquals($navHtml, nav($links));
    }
    
    public function testZeroMaxDepthCutsOffNestedNav()
    {
        $links = array(
            'Browse Items' => '/items', 
            'Browse Collections' => array(
                'uri' => '/collections', 
                'subnav_links' => array(
                    'Collection 1' => '/collections/1',
                    'Collection 2' => '/collections/2',
                    'Collection 3' => '/collections/3'), 
                'subnav_attributes' => array('class' => 'subnav1')
            ),
            'Home' => '/'
        );

        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a>'; 
        $navHtml .= '</li>' . "\n";
        $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
        $this->assertEquals($navHtml, nav($links, 0));
    }

    public function testNestedNavigation()
    {
        $links = array(
            'Browse Items' => '/items', 
            'Browse Collections' => array(
                'uri' => '/collections', 
                'subnav_links' => array(
                    'Collection 1' => '/collections/1',
                    'Collection 2' => '/collections/2',
                    'Collection 3' => '/collections/3'), 
                'subnav_attributes' => array('class' => 'subnav1')
            ),
            'Home' => '/'
        );

        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a>' . "\n"; 
        $navHtml .= '<ul class="subnav1">' . "\n";
        $navHtml .= '<li class="nav-collection-1"><a href="/collections/1">Collection 1</a></li>' . "\n";
        $navHtml .= '<li class="nav-collection-2"><a href="/collections/2">Collection 2</a></li>' . "\n";
        $navHtml .= '<li class="nav-collection-3"><a href="/collections/3">Collection 3</a></li>' . "\n";
        $navHtml .= '</ul>' . "\n";
        $navHtml .= '</li>' . "\n";
        $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
        $this->assertEquals($navHtml, nav($links, null));
    }

    public function testDeeplyNestedNavigation()
    {
        $links = array(
            'Browse Items' => '/items',         
            'Browse Collections' => array(
                'uri' => '/collections', 
                'subnav_links' => array(
                    'Collection 1' => '/collections/1',
                    'Collection 2' => array(
                        'uri' => '/collections/2',
                        'subnav_links' => array(
                            'Item 1' => '/items/1',
                            'Item 2' => '/items/2'
                        ),
                        'subnav_attributes' => array()
                    ),
                    'Collection 3' => '/collections/3'
                ), 
                'subnav_attributes' => array('class' => 'subnav1')
            ),
            'Home' => '/'
        );

        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a>' . "\n"; 
        $navHtml .= '<ul class="subnav1">' . "\n";
        $navHtml .= '<li class="nav-collection-1"><a href="/collections/1">Collection 1</a></li>' . "\n";
        $navHtml .= '<li class="nav-collection-2"><a href="/collections/2">Collection 2</a>' . "\n";
        $navHtml .= '<ul>' . "\n";
        $navHtml .= '<li class="nav-item-1"><a href="/items/1">Item 1</a></li>' . "\n";
        $navHtml .= '<li class="nav-item-2"><a href="/items/2">Item 2</a></li>' . "\n";            
        $navHtml .= '</ul>' . "\n";            
        $navHtml .= '</li>' . "\n";
        $navHtml .= '<li class="nav-collection-3"><a href="/collections/3">Collection 3</a></li>' . "\n";
        $navHtml .= '</ul>' . "\n";
        $navHtml .= '</li>' . "\n";
        $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
        $this->assertEquals($navHtml, nav($links, null));
    }
}

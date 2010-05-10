<?php
require_once HELPERS;

/**
 * Tests nav($links)
 * in helpers/LinkFunctions.php
 */ 
class Helpers_LinkFunctions_NavTest extends Omeka_Test_AppTestCase
{
    public function testNavWithEmptyLinkArray()
    {
        $links = array();
        
        $navHtml = '';
        
        $this->dispatch('/');
        $this->assertEquals($navHtml, nav($links, null));
    }
    
    public function testNavWithSimpleLinkArray()
    {
        $links = array('Browse Items' => '/items', 'Browse Collections' => '/collections', 'Home' => '/');
        
        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a></li>' . "\n";
        $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
        $this->dispatch('/');
        $this->assertEquals($navHtml, nav($links, null));
        $this->assertEquals($navHtml, nav($links, 0));
        $this->assertEquals($navHtml, nav($links, 10));
        
                
        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections current"><a href="/collections">Browse Collections</a></li>' . "\n";
        $navHtml .= '<li class="nav-home"><a href="/">Home</a></li>' . "\n";
        $this->dispatch('/collections');
        $this->assertEquals($navHtml, nav($links, null));
        $this->assertEquals($navHtml, nav($links, 0));
        $this->assertEquals($navHtml, nav($links, 10));
    }
    
    public function testNavWithSimpleLinkArrayWithoutUrlsForSomeLinks()
    {
        $links = array('Browse Items' => '/items', 'Browse Collections' => '', 'Home' => '/');
        
        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections">Browse Collections</li>' . "\n";
        $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
        $this->dispatch('/');
        $this->assertEquals($navHtml, nav($links, null));
        $this->assertEquals($navHtml, nav($links, 0));
        $this->assertEquals($navHtml, nav($links, 10));
        
        $navHtml = '';
        $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
        $navHtml .= '<li class="nav-browse-collections">Browse Collections</li>' . "\n";
        $navHtml .= '<li class="nav-home"><a href="/">Home</a></li>' . "\n";
        $this->dispatch('/collections');
        $this->assertEquals($navHtml, nav($links, null));
        $this->assertEquals($navHtml, nav($links, 0));
        $this->assertEquals($navHtml, nav($links, 10));
    }
    
    public function testNavWithSubNavigationLinksArray()
    {
            $links = array('Browse Items' => '/items', 
                           'Browse Collections' => array('uri' => '/collections', 
                                                         'subnav_links' => array('Collection 1' => '/collections/1',
                                                                                 'Collection 2' => '/collections/2',
                                                                                 'Collection 3' => '/collections/3'), 
                                                         'subnav_attributes' => array('class' => 'subnav1')),
                           'Home' => '/');

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
            $this->dispatch('/');
            $this->assertEquals($navHtml, nav($links, null));
            $this->assertEquals($navHtml, nav($links, 1));
            $this->assertNotEquals($navHtml, nav($links, 0));
            
            
            $navHtml = '';
            $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
            $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a>'; 
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
            $this->dispatch('/');
            $this->assertEquals($navHtml, nav($links, 0));
    }
    
    public function testNavWithSubNavigationLinksArrayWithoutUrlForLinkWithSubNavLinks()
    {
            $links = array('Browse Items' => '/items', 
                           'Browse Collections' => array('uri' => '', 
                                                         'subnav_links' => array('Collection 1' => '/collections/1',
                                                                                 'Collection 2' => '/collections/2',
                                                                                 'Collection 3' => '/collections/3'), 
                                                         'subnav_attributes' => array('class' => 'subnav1')),
                           'Home' => '/');

            $navHtml = '';
            $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
            $navHtml .= '<li class="nav-browse-collections">Browse Collections' . "\n"; 
            $navHtml .= '<ul class="subnav1">' . "\n";
            $navHtml .= '<li class="nav-collection-1"><a href="/collections/1">Collection 1</a></li>' . "\n";
            $navHtml .= '<li class="nav-collection-2"><a href="/collections/2">Collection 2</a></li>' . "\n";
            $navHtml .= '<li class="nav-collection-3"><a href="/collections/3">Collection 3</a></li>' . "\n";
            $navHtml .= '</ul>' . "\n";
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
            $this->dispatch('/');
            $this->assertEquals($navHtml, nav($links, null));
            $this->assertEquals($navHtml, nav($links, 1));
            $this->assertNotEquals($navHtml, nav($links, 0));

            
            $navHtml = '';
            $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
            $navHtml .= '<li class="nav-browse-collections">Browse Collections'; 
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
            $this->dispatch('/');
            $this->assertEquals($navHtml, nav($links, 0));
    }
    
    public function testNavWithSubNavigationLinksArrayWithoutUrlsForSomeLinksWithSubNavLinks()
    {
            $collection = new Collection();
            $collection->name = 'Collection 1';
            $collection->description = '';
            $collection->public = 1;
            $collection->save();
            
            $this->assertEquals(1, $collection->id);
                        
            $links = array('Browse Items' => '/items', 
                           'Browse Collections' => array('uri' => '/collections/index', 
                                                         'subnav_links' => array('Collection 1' => '/collections/show/1',
                                                                                 'Collection 2' => '',
                                                                                 'Collection 3' => '/collections/show/3'), 
                                                         'subnav_attributes' => array('class' => 'subnav1')),
                           'Home' => '/');

            $navHtml = '';
            $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
            $navHtml .= '<li class="nav-browse-collections"><a href="/collections/index">Browse Collections</a>' . "\n"; 
            $navHtml .= '<ul class="subnav1">' . "\n";
            $navHtml .= '<li class="nav-collection-1 current"><a href="/collections/show/1">Collection 1</a></li>' . "\n";
            $navHtml .= '<li class="nav-collection-2">Collection 2</li>' . "\n";
            $navHtml .= '<li class="nav-collection-3"><a href="/collections/show/3">Collection 3</a></li>' . "\n";
            $navHtml .= '</ul>' . "\n";
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-home"><a href="/">Home</a></li>' . "\n";
            $this->dispatch('/collections/show/1');
            $this->assertEquals($navHtml, nav($links, null));
            $this->assertEquals($navHtml, nav($links, 1));
            $this->assertNotEquals($navHtml, nav($links, 0));

            
            $navHtml = '';
            $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
            $navHtml .= '<li class="nav-browse-collections"><a href="/collections/index">Browse Collections</a>'; 
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-home"><a href="/">Home</a></li>' . "\n";
            $this->dispatch('/collections/show/1');
            $this->assertEquals($navHtml, nav($links, 0));
    }
    
    public function testNavWithMultipleSubNavigationLinksArray()
    {
            $links = array('Browse Items' => '/items', 
                           'Browse Collections' => array('uri' => '/collections', 
                                                         'subnav_links' => array('Collection 1' => '/collections/1',
                                                                                 'Collection 2' => array('uri' => '/collections/2',
                                                                                                         'subnav_links' => array('Item 1' => '/items/1',
                                                                                                                                 'Item 2' => '/items/2'),
                                                                                                         'subnav_attributes' => array()),
                                                                                 'Collection 3' => '/collections/3'), 
                                                         'subnav_attributes' => array('class' => 'subnav1')),
                           'Home' => '/');

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
            $this->dispatch('/');
            $this->assertEquals($navHtml, nav($links, null));
            $this->assertEquals($navHtml, nav($links, 2));
            $this->assertNotEquals($navHtml, nav($links, 0));
            $this->assertNotEquals($navHtml, nav($links, 1));
            
            $navHtml = '';
            $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
            $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a>' . "\n"; 
            $navHtml .= '<ul class="subnav1">' . "\n";
            $navHtml .= '<li class="nav-collection-1"><a href="/collections/1">Collection 1</a></li>' . "\n";
            $navHtml .= '<li class="nav-collection-2"><a href="/collections/2">Collection 2</a>';          
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-collection-3"><a href="/collections/3">Collection 3</a></li>' . "\n";
            $navHtml .= '</ul>' . "\n";
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
            $this->dispatch('/');
            $this->assertEquals($navHtml, nav($links, 1));
            $this->assertNotEquals($navHtml, nav($links, 0));
            $this->assertNotEquals($navHtml, nav($links, 2));
            
            $navHtml = '';
            $navHtml .= '<li class="nav-browse-items"><a href="/items">Browse Items</a></li>' . "\n";
            $navHtml .= '<li class="nav-browse-collections"><a href="/collections">Browse Collections</a>'; 
            $navHtml .= '</li>' . "\n";
            $navHtml .= '<li class="nav-home current"><a href="/">Home</a></li>' . "\n";
            $this->dispatch('/');
            $this->assertEquals($navHtml, nav($links, 0));
            $this->assertNotEquals($navHtml, nav($links, 1));
            $this->assertNotEquals($navHtml, nav($links, 2));
    }
}
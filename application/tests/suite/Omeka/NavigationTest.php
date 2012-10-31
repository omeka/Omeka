<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_NavigationTest extends Omeka_Test_AppTestCase
{
    protected $_nav;
    
    public function setUp()
    {
        parent::setUp();
        $this->_nav = new Omeka_Navigation();
    }
    
    public function testEmptyOmekaNavigation()
    {
       $this->assertEquals(0, $this->_nav->count());
       $this->assertEquals(false, $this->_nav->hasChildren());
       $this->assertEquals(null, $this->_nav->getChildren());
       $this->assertEquals(false, $this->_nav->hasPages());
       $this->assertEquals(array(), $this->_nav->getPages());
       $this->assertEquals(array(), $this->_nav->toArray());
    }
    
    public function testAddSingleExplicitVisibleZendNavigationPageMvc()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => __('Browse Items'),
            'controller' => 'items',
            'action' => 'browse',
            'visible' => true
        ));
        
        $this->assertNull($page->get('uid'));
        
        $this->_nav->addPage($page);
        
        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';
        $this->assertEquals($uid, $page->get('uid'));
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertEquals($page, $firstChild);
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals('items', $firstChild->getController());
        $this->assertEquals('browse', $firstChild->getAction());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        $this->assertContainsOnly('Zend_Navigation_Page_Mvc', $pages);
        $this->assertContains($page, $pages);
        
        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        
        $pageArray = $page->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleImplicitVisibleZendNavigationPageMvc()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => __('Browse Items'),
            'controller' => 'items',
            'action' => 'browse',
        ));
        
        $this->assertNull($page->get('uid'));
        
        $this->_nav->addPage($page);
        
        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';
        $this->assertEquals($uid, $page->get('uid'));
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertEquals($page, $firstChild);
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals('items', $firstChild->getController());
        $this->assertEquals('browse', $firstChild->getAction());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        $this->assertContainsOnly('Zend_Navigation_Page_Mvc', $pages);
        $this->assertContains($page, $pages);
        
        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        
        $pageArray = $page->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleExplicitNotVisibleZendNavigationPageMvc()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => __('Browse Items'),
            'controller' => 'items',
            'action' => 'browse',
            'visible' => false
        ));
        
        $this->assertNull($page->get('uid'));
        
        $this->_nav->addPage($page);
        
        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';
        $this->assertEquals($uid, $page->get('uid'));
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertEquals($page, $firstChild);
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals('items', $firstChild->getController());
        $this->assertEquals('browse', $firstChild->getAction());
        $this->assertFalse($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        $this->assertContainsOnly('Zend_Navigation_Page_Mvc', $pages);
        $this->assertContains($page, $pages);
        
        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        
        $pageArray = $page->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleExplicitVisibleZendNavigationPageUri()
    {
        $page = new Zend_Navigation_Page_Uri(array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
            'visible' => true
        ));
        
        $this->assertNull($page->get('uid'));
        
        $this->_nav->addPage($page);
        
        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';
        
        // this page should not be altered because it should create a new Omeka_Zend_Navigation_Page_Uri
        $this->assertNull($page->get('uid'));         
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        // it should convert the Zend_Navigation_Page_Uri to an 
        // Omeka_Navigation_Page_Uri page
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleImplicitVisibleZendNavigationPageUri()
    {
        $page = new Zend_Navigation_Page_Uri(array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
        ));
        
        $this->assertNull($page->get('uid'));
        
        $this->_nav->addPage($page);
        
        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';
        
        // this page should not be altered because it should create a new Omeka_Zend_Navigation_Page_Uri
        $this->assertNull($page->get('uid'));         
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        // it should convert the Zend_Navigation_Page_Uri to an 
        // Omeka_Navigation_Page_Uri page
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleExplicitNotVisibleZendNavigationPageUri()
    {
        $page = new Zend_Navigation_Page_Uri(array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
            'visible' => false
        ));
        
        $this->assertNull($page->get('uid'));
        
        $this->_nav->addPage($page);
        
        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';
        
        // this page should not be altered because it should create a new Omeka_Zend_Navigation_Page_Uri
        $this->assertNull($page->get('uid'));         
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertFalse($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        // it should convert the Zend_Navigation_Page_Uri to an 
        // Omeka_Navigation_Page_Uri page
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }    
    
    public function testAddSingleExplicitVisibleUriArray()
    {
        $page = array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
            'visible' => true
        );

        $this->_nav->addPage($page);

        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';       

        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());

        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);

        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());

        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());

        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();

        // it should convert the Zend_Navigation_Page_Uri to an 
        // Omeka_Navigation_Page_Uri page
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);

        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }

    public function testAddSingleImplicitVisibleUriArray()
    {
        $page = array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
        );

        $this->_nav->addPage($page);

        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';     

        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());

        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);

        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());

        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());

        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();

        // it should convert the Zend_Navigation_Page_Uri to an 
        // Omeka_Navigation_Page_Uri page
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);

        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }

    public function testAddSingleExplicitNotVisibleUriArray()
    {
        $page = array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
            'visible' => false
        );

        $this->_nav->addPage($page);

        $this->assertEquals(CURRENT_BASE_URL, PUBLIC_BASE_URL);
        $uid = PUBLIC_BASE_URL . '/items/browse';        

        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());

        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);

        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertFalse($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());

        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());

        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();

        // it should convert the Zend_Navigation_Page_Uri to an 
        // Omeka_Navigation_Page_Uri page
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);

        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
}
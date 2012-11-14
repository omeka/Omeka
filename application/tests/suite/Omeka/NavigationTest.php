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
        $this->_beforePages = array();
    }
    
    public function testEmptyOmekaNavigation()
    {
       $this->assertEquals(0, $this->_nav->count());
       $this->assertFalse($this->_nav->hasChildren());
       $this->assertNull($this->_nav->getChildren());
       $this->assertFalse($this->_nav->hasPages());
       $this->assertEmpty($this->_nav->getPages());
       $this->assertEmpty($this->_nav->toArray());
    }
    
    public function testAddSingleExplicitVisibleOmekaNavigationPageMvc()
    {
        $page = new Omeka_Navigation_Page_Mvc(array(
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
        $this->assertContainsOnly('Omeka_Navigation_Page_Mvc', $pages);
        $this->assertContains($page, $pages);
        
        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        
        $pageArray = $page->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleImplicitVisibleOmekaNavigationPageMvc()
    {
        $page = new Omeka_Navigation_Page_Mvc(array(
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
        $this->assertContainsOnly('Omeka_Navigation_Page_Mvc', $pages);
        $this->assertContains($page, $pages);
        
        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        
        $pageArray = $page->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleExplicitNotVisibleOmekaNavigationPageMvc()
    {
        $page = new Omeka_Navigation_Page_Mvc(array(
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
        $this->assertContainsOnly('Omeka_Navigation_Page_Mvc', $pages);
        $this->assertContains($page, $pages);
        
        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        
        $pageArray = $page->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
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
        
        // this page should not be altered because it should create a new Omeka_Zend_Navigation_Page_Mvc
        $this->assertNull($page->get('uid'));         
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Mvc', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals('items', $firstChild->getController());
        $this->assertEquals('browse', $firstChild->getAction());        
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        // it should convert the Zend_Navigation_Page_Mvc to an 
        // Omeka_Navigation_Page_Mvc page
        $this->assertContainsOnly('Omeka_Navigation_Page_Mvc', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
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
        
        // this page should not be altered because it should create a new Omeka_Zend_Navigation_Page_Mvc
        $this->assertNull($page->get('uid'));         
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Mvc', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals('items', $firstChild->getController());
        $this->assertEquals('browse', $firstChild->getAction());        
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        // it should convert the Zend_Navigation_Page_Mvc to an 
        // Omeka_Navigation_Page_Mvc page
        $this->assertContainsOnly('Omeka_Navigation_Page_Mvc', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
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
        
        // this page should not be altered because it should create a new Omeka_Zend_Navigation_Page_Mvc
        $this->assertNull($page->get('uid'));         
        
        $this->assertEquals(1, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        
        $firstChild = $this->_nav->getChildren();
        $this->assertNotEquals($page, $firstChild);
        $this->assertInstanceOf('Omeka_Navigation_Page_Mvc', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals('items', $firstChild->getController());
        $this->assertEquals('browse', $firstChild->getAction());        
        $this->assertFalse($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        // it should convert the Zend_Navigation_Page_Mvc to an 
        // Omeka_Navigation_Page_Mvc page
        $this->assertContainsOnly('Omeka_Navigation_Page_Mvc', $pages);
        $this->assertNotContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertNotEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
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
    
    public function testAddSingleExplicitVisibleOmekaNavigationPageUri()
    {
        $page = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
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
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleImplicitVisibleOmekaNavigationPageUri()
    {
        $page = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
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
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertTrue($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddSingleExplicitNotVisibleOmekaNavigationPageUri()
    {
        $page = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Browse Items'),
            'uri' => url('items/browse'),
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
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $firstChild);
        
        $this->assertEquals(__('Browse Items'), $firstChild->getLabel());
        $this->assertEquals(url('items/browse'), $firstChild->getUri());
        $this->assertFalse($firstChild->getVisible());
        $this->assertEquals(0, $firstChild->getOrder());
        
        $this->assertEquals($uid, $firstChild->get('uid'));
        $this->assertEquals($uid, $firstChild->getHref());
            
        $this->assertTrue($this->_nav->hasPages());
        $pages = $this->_nav->getPages();
        
        $this->assertContainsOnly('Omeka_Navigation_Page_Uri', $pages);
        $this->assertContains($page, $pages);
        $this->assertContains($firstChild, $pages);

        $foundPage = $this->_nav->findOneBy('uid', $uid);
        $this->assertEquals($page, $foundPage);
        $this->assertEquals($firstChild, $foundPage);
        
        $pageArray = $firstChild->toArray();
        $this->assertContains($pageArray, $this->_nav->toArray());
    }
    
    public function testAddDifferentPagesFlatArray()
    {
        $explicitVisibleZendNavPageMvc = new Omeka_Navigation_Page_Mvc(array(
            'label' => __('Browse Items'),
            'controller' => 'items',
            'action' => 'browse',
            'visible' => true
        ));
        
        $implicitVisibleZendNavPageUri = new Zend_Navigation_Page_Uri(array(
            'label' => __('Browse Collections'),
            'uri' => url('collections/browse'),
        ));
        
        $notVisibleNavPageUriArray = array(
            'label' => __('Edit Items'),
            'uri' => url('items/edit'),
            'visible' => false
        );
        
        $notVisibleOmekaNavPageUri = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Edit Collections'),
            'uri' => url('collections/edit'),
            'visible' => false
        ));
        
        $explicitVisibleOmekaNavPageUri = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Omeka'),
            'uri' => 'http://omeka.org',
            'visible' => true
        ));
        
        $pages = array(
            $explicitVisibleZendNavPageMvc,
            $implicitVisibleZendNavPageUri,
            $notVisibleNavPageUriArray,
            $notVisibleOmekaNavPageUri,
            $explicitVisibleOmekaNavPageUri
        );
                
        $this->_nav->addPages($pages);
        
        $this->assertEquals(5, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        $this->assertTrue($this->_nav->hasPages());
        
        $addedPages = $this->_nav->getPages();
        $this->assertCount(5, $addedPages);
        
        $afterPages = $this->_getSimpleArray($addedPages);
        $afterPage1 = $afterPages[0];
        $afterPage2 = $afterPages[1];
        $afterPage3 = $afterPages[2];
        $afterPage4 = $afterPages[3];
        $afterPage5 = $afterPages[4];
        
        $this->assertEquals($explicitVisibleZendNavPageMvc, $afterPage1);
        $this->assertNotEquals($implicitVisibleZendNavPageUri, $afterPage2);
        $this->assertNotEquals($notVisibleNavPageUriArray, $afterPage3);
        $this->assertEquals($notVisibleOmekaNavPageUri, $afterPage4);
        $this->assertEquals($explicitVisibleOmekaNavPageUri, $afterPage5);

        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage2);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage3);

        // order is null unless explicitly specified
        $this->assertNull($afterPage1->getOrder());
        $this->assertNull($afterPage2->getOrder());
        $this->assertNull($afterPage3->getOrder());
        $this->assertNull($afterPage4->getOrder());
        $this->assertNull($afterPage5->getOrder());
        
        $this->assertEquals(url('items/browse'), $afterPage1->uid);
        $this->assertEquals(url('collections/browse'), $afterPage2->uid);
        $this->assertEquals(url('items/edit'), $afterPage3->uid);
        $this->assertEquals(url('collections/edit'), $afterPage4->uid);
        $this->assertEquals('http://omeka.org', $afterPage5->uid);

        $this->assertEquals(__('Browse Items'), $afterPage1->getLabel());
        $this->assertEquals(__('Browse Collections'), $afterPage2->getLabel());
        $this->assertEquals(__('Edit Items'), $afterPage3->getLabel());
        $this->assertEquals(__('Edit Collections'), $afterPage4->getLabel());
        $this->assertEquals(__('Omeka'), $afterPage5->getLabel());
    }
    
    public function testAddDifferentPagesNestedArray()
    {
        $explicitVisibleOmekaNavPageUri = new Omeka_Navigation_Page_Uri(array(
            'label' => __('CHNM'),
            'uri' => 'http://chnm.gmu.edu',
            'visible' => true
        ));
        
        $implicitVisibleZendNavPageUri = new Zend_Navigation_Page_Uri(array(
            'label' => __('Browse Collections'),
            'uri' => url('collections/browse'),
            'pages' => array(
                $explicitVisibleOmekaNavPageUri
            )
        ));
        
        $notVisibleNavPageUriArray = array(
            'label' => __('Edit Items'),
            'uri' => url('items/edit'),
            'visible' => false
        );
        
        $explicitVisibleZendNavPageMvc = new Omeka_Navigation_Page_Mvc(array(
            'label' => __('Browse Items'),
            'controller' => 'items',
            'action' => 'browse',
            'visible' => true,
            'pages' => array(
                $implicitVisibleZendNavPageUri,
                $notVisibleNavPageUriArray,
            )
        ));
        
        $notVisibleOmekaNavPageUri = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Edit Collections'),
            'uri' => url('collections/edit'),
            'visible' => false
        ));
        
        $explicitVisibleOmekaNavPageUri2 = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Omeka'),
            'uri' => 'http://omeka.org',
            'visible' => true
        ));
        
        $pages = array(
            $explicitVisibleZendNavPageMvc,
            $notVisibleOmekaNavPageUri,
            $explicitVisibleOmekaNavPageUri2,
        );
        
        $this->assertEquals(0, $this->_nav->count());
        
        $this->_nav->addPages($pages);
        
        $this->assertEquals(3, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        $this->assertTrue($this->_nav->hasPages());
        
        $addedPages = $this->_nav->getPages();
        $this->assertCount(3, $addedPages);
        
        $afterPages = $this->_getSimpleArray($addedPages);
        
        $afterPage1 = $afterPages[0];
        $afterPage2 = $afterPages[1];
        $afterPage3 = $afterPages[2];
        
        $this->assertEquals(2, $afterPage1->count());
        $this->assertTrue($afterPage1->hasChildren());
        $this->assertTrue($afterPage1->hasPages());
        $this->assertEquals(0, $afterPage2->count());
        $this->assertFalse($afterPage2->hasChildren());
        $this->assertFalse($afterPage2->hasPages());
        $this->assertEquals(0, $afterPage3->count());
        $this->assertFalse($afterPage3->hasChildren());
        $this->assertFalse($afterPage3->hasPages());

        $afterPage1Pages = $afterPage1->getPages();
        $this->assertCount(2, $afterPage1Pages);
        $afterPages = $this->_getSimpleArray($afterPage1Pages);
        
        $afterPage1Page1 = $afterPages[0];
        $afterPage1Page2 = $afterPages[1];
        
        $this->assertEquals(1, $afterPage1Page1->count());
        $this->assertTrue($afterPage1Page1->hasChildren());
        $this->assertTrue($afterPage1Page1->hasPages());
        $this->assertEquals(0, $afterPage1Page2->count());
        $this->assertFalse($afterPage1Page2->hasChildren());
        $this->assertFalse($afterPage1Page2->hasPages());
        
        $afterPage1Page1Pages = $afterPage1Page1->getPages();
        $this->assertCount(1, $afterPage1Page1Pages);
        $afterPages = $this->_getSimpleArray($afterPage1Page1Pages);
        $afterPage1Page1Page1 = $afterPages[0];
        
        $this->assertEquals(0, $afterPage1Page1Page1->count());
        $this->assertFalse($afterPage1Page1Page1->hasChildren());
        $this->assertFalse($afterPage1Page1Page1->hasPages());
        
        $this->assertEquals($explicitVisibleZendNavPageMvc, $afterPage1);
        $this->assertNotEquals($implicitVisibleZendNavPageUri, $afterPage1Page1);
        $this->assertNotEquals($explicitVisibleOmekaNavPageUri, $afterPage1Page1Page1);
        $this->assertNotEquals($notVisibleNavPageUriArray, $afterPage1Page2);
        $this->assertEquals($notVisibleOmekaNavPageUri, $afterPage2);
        $this->assertEquals($explicitVisibleOmekaNavPageUri2, $afterPage3);

        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage1Page1);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage1Page1Page1);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage1Page2);

        // order is null unless explicitly specified
        $this->assertNull($afterPage1->getOrder());
        $this->assertNull($afterPage1Page1->getOrder());
        $this->assertNull($afterPage1Page1Page1->getOrder());
        $this->assertNull($afterPage1Page2->getOrder());
        $this->assertNull($afterPage2->getOrder());
        $this->assertNull($afterPage3->getOrder());
        
        $this->assertEquals(url('items/browse'), $afterPage1->uid);
        $this->assertEquals(url('collections/browse'), $afterPage1Page1->uid);
        $this->assertEquals('http://chnm.gmu.edu', $afterPage1Page1Page1->uid);
        $this->assertEquals(url('items/edit'), $afterPage1Page2->uid);
        $this->assertEquals(url('collections/edit'), $afterPage2->uid);
        $this->assertEquals('http://omeka.org', $afterPage3->uid);

        $this->assertEquals(__('Browse Items'), $afterPage1->getLabel());
        $this->assertEquals(__('Browse Collections'), $afterPage1Page1->getLabel());
        $this->assertEquals(__('CHNM'), $afterPage1Page1Page1->getLabel());
        $this->assertEquals(__('Edit Items'), $afterPage1Page2->getLabel());
        $this->assertEquals(__('Edit Collections'), $afterPage2->getLabel());
        $this->assertEquals(__('Omeka'), $afterPage3->getLabel());
    }
    
    protected function _getSimpleArray($a)
    {
        $aa = array();
        foreach($a as $k => $v) {
            $aa[] = $v;
        }
        return $aa;
    }
    
    public function testCreatePageUid()
    {
        $href = "http://omeka.org";
        $this->assertEquals($href, $this->_nav->createPageUid($href));
        
        $href = "/omeka.org";
        $this->assertEquals($href, $this->_nav->createPageUid($href));
    }
    
    public function testGetPageByUidFlatArray()
    {
        $this->_addFlatPagesArray();
                
        $testPageInfos = array();
        $hrefPageIds = array(1,2,4,5);
        foreach($hrefPageIds as $pageId) {
            $page = $this->_beforePages[__('Page ' . $pageId)];
            $testPageInfos[] = array(
                'label' => $page->getLabel(),
                'href' => $page->getHref(),
            );
        }
        $arrayPageIds = array(3);
        foreach($arrayPageIds as $pageId) {
            $page = $this->_beforePages[__('Page ' . $pageId)];
            $testPageInfos[] = array(
                'label' => $page['label'],
                'href' => $page['uri'],
            );
        }
                
        foreach($testPageInfos as $pageInfo) {
            $uid = $this->_nav->createPageUid($pageInfo['href']);            
            $retrievedPage = $this->_nav->getPageByUid($uid);
            $this->assertEquals($pageInfo['label'], $retrievedPage->getLabel());
            $this->assertEquals($uid, $retrievedPage->uid);
        }
    }


    protected function _addNestedPagesArray()
    {                
        $page1 = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Page 1'),
            'uri' => 'http://chnm.gmu.edu',
            'visible' => true
        ));        
        $this->_beforePages[__('Page 1')] = $page1;
        
        $page2 = new Zend_Navigation_Page_Uri(array(
            'label' => __('Page 2'),
            'uri' => url('collections/browse'),
            'pages' => array(
                $page1
            )
        ));
        $this->_beforePages[__('Page 2')] = $page2;
        
        $page3 = array(
            'label' => __('Page 3'),
            'uri' => url('items/edit'),
            'visible' => false
        );
        $this->_beforePages[__('Page 3')] = $page3;
        
        $page4 = new Omeka_Navigation_Page_Mvc(array(
            'label' => __('Page 4'),
            'controller' => 'items',
            'action' => 'browse',
            'visible' => true,
            'pages' => array(
                $page2,
                $page3,
            )
        ));
        $this->_beforePages[__('Page 4')] = $page4;
        
        $page5 = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Page 5'),
            'uri' => url('collections/edit'),
            'visible' => false
        ));
        $this->_beforePages[__('Page 5')] = $page5;
        
        $page6 = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Page 6'),
            'uri' => 'http://omeka.org',
            'visible' => true
        ));
        $this->_beforePages[__('Page 6')] = $page6;
        
        $pages = array(
            $page4,
            $page5,
            $page6
        );

        $this->assertEquals(0, $this->_nav->count());
        $this->_nav->addPages($pages);
        $this->assertEquals(3, $this->_nav->count());
    }
    
    protected function _addFlatPagesArray()
    {
        $page1 = new Omeka_Navigation_Page_Mvc(array(
            'label' => __('Page 1'),
            'controller' => 'items',
            'action' => 'browse',
            'visible' => true
        ));
        $this->_beforePages[__('Page 1')] = $page1;

        $page2 = new Zend_Navigation_Page_Uri(array(
            'label' => __('Page 2'),
            'uri' => url('collections/browse'),
        ));
        $this->_beforePages[__('Page 2')] = $page2;

        $page3 = array(
            'label' => __('Page 3'),
            'uri' => url('items/edit'),
            'visible' => false
        );
        $this->_beforePages[__('Page 3')] = $page3;

        $page4 = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Page 4'),
            'uri' => url('collections/edit'),
            'visible' => false
        ));
        $this->_beforePages[__('Page 4')] = $page4;

        $page5 = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Page 5'),
            'uri' => 'http://omeka.org',
            'visible' => true
        ));
        $this->_beforePages[__('Page 5')] = $page5;

        $pages = array(
           $page1,
           $page2,
           $page3,
           $page4,
           $page5
        );

        $this->assertEquals(0, $this->_nav->count());
        $this->_nav->addPages($pages);
        $this->assertEquals(5, $this->_nav->count());
    }
    
    public function testGetPageByUidNestedArray()
    {   
        $this->_addNestedPagesArray();
 
        $addedPages = $this->_nav->getPages();
        $this->assertCount(3, $addedPages);

        $testPageInfos = array();
        $hrefPageIds = array(1,2,4,5,6);
        foreach($hrefPageIds as $pageId) {
            $page = $this->_beforePages[__('Page ' . $pageId)];
            $testPageInfos[] = array(
                'label' => $page->getLabel(),
                'href' => $page->getHref(),
            );
        }
        $arrayPageIds = array(3);
        foreach($arrayPageIds as $pageId) {
            $page = $this->_beforePages[__('Page ' . $pageId)];
            $testPageInfos[] = array(
                'label' => $page['label'],
                'href' => $page['uri'],
            );
        }
        
        foreach($testPageInfos as $pageInfo) {
            $uid = $this->_nav->createPageUid($pageInfo['href']);            
            $retrievedPage = $this->_nav->getPageByUid($uid);
            $this->assertEquals($pageInfo['label'], $retrievedPage->getLabel());
            $this->assertEquals($uid, $retrievedPage->uid);
        }        
    }
    
    public function testPrunePageFlatArray()
    {
        $this->_addFlatPagesArray();
        
        // test pruning first page
        $beforePrunePage = $this->_beforePages[__('Page 1')];
        $prunePage = $this->_nav->getPageByUid($beforePrunePage->getHref());
        $this->_nav->prunePage($prunePage);
        $afterPrunePages = $this->_nav->getPages();
        $this->assertNotContains($prunePage, $afterPrunePages);
        $this->assertCount(4, $afterPrunePages);
        $labels = array(
            __('Page 2'),
            __('Page 3'),
            __('Page 4'),
            __('Page 5'),
        );
        $i = 0;
        foreach($afterPrunePages as $afterPrunePage) {
            $this->assertEquals($labels[$i], $afterPrunePage->getLabel());
            $i++;
        }
        
        // test pruning last page
        $beforePrunePage = $this->_beforePages[__('Page 5')];
        $prunePage = $this->_nav->getPageByUid($beforePrunePage->getHref());
        $this->_nav->prunePage($prunePage);
        $afterPrunePages = $this->_nav->getPages();
        $this->assertNotContains($prunePage, $afterPrunePages);
        $this->assertCount(3, $afterPrunePages);
        $labels = array(
            __('Page 2'),
            __('Page 3'),
            __('Page 4'),
        );
        $i = 0;
        foreach($afterPrunePages as $afterPrunePage) {
            $this->assertEquals($labels[$i], $afterPrunePage->getLabel());
            $i++;
        }
        
        // test pruning middle page
        $beforePrunePage = $this->_beforePages[__('Page 3')];
        $prunePage = $this->_nav->getPageByUid($beforePrunePage['uri']);
        $this->_nav->prunePage($prunePage);
        $afterPrunePages = $this->_nav->getPages();
        $this->assertNotContains($prunePage, $afterPrunePages);
        $this->assertCount(2, $afterPrunePages);
        $labels = array(
            __('Page 2'),
            __('Page 4'),
        );
        $i = 0;
        foreach($afterPrunePages as $afterPrunePage) {
            $this->assertEquals($labels[$i], $afterPrunePage->getLabel());
            $i++;
        }
    }
    
    public function testPrunePageNestedArray()
    {
        $this->_addNestedPagesArray();
        
        // prune a page without a subpage
        $beforePrunePage = $this->_beforePages[__('Page 6')];
        $prunePage = $this->_nav->getPageByUid($beforePrunePage->getHref());
        $this->_nav->prunePage($prunePage);
        $afterPrunePages = $this->_nav->getPages();
        $this->assertNotContains($prunePage, $afterPrunePages);
        $this->assertCount(2, $afterPrunePages);
        $labels = array(
            __('Page 4'),
            __('Page 5'),
        );
        $i = 0;
        foreach($afterPrunePages as $afterPrunePage) {
            $this->assertEquals($labels[$i], $afterPrunePage->getLabel());
            $i++;
        }
        
        // prune a page with a subpage
        $beforePrunePage = $this->_beforePages[__('Page 2')];
        $prunePage = $this->_nav->getPageByUid($beforePrunePage->getHref());
        $this->_nav->prunePage($prunePage);
        $afterPrunePages = $this->_nav->getPages();
        $this->assertNotContains($prunePage, $afterPrunePages);
        $this->assertNull($this->_nav->getPageByUid($beforePrunePage->getHref()));
        $this->assertCount(2, $afterPrunePages);
        $labels = array(
            __('Page 4'),
            __('Page 5'),
        );
        $i = 0;
        foreach($afterPrunePages as $afterPrunePage) {
            $this->assertEquals($labels[$i], $afterPrunePage->getLabel());
            $i++;
        }
        
        // make sure page 1 is now appended to the bottom of page 4
        $beforePage4 = $this->_beforePages[__('Page 4')];
        $page4 = $this->_nav->getPageByUid($beforePage4->getHref());
        $childPages = $page4->getPages();
        $this->assertCount(2, $childPages);
        $labels = array(
            __('Page 3'),
            __('Page 1'),
        );
        $i = 0;
        foreach($childPages as $page) {
            $this->assertEquals($labels[$i], $page->getLabel());
            $i++;
        }
    }
}
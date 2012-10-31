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
       $this->assertFalse($this->_nav->hasChildren());
       $this->assertNull($this->_nav->getChildren());
       $this->assertFalse($this->_nav->hasPages());
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
    
    public function addDifferentPagesFlatArray()
    {
        $explicitVisibleZendNavPageMvc = new Zend_Navigation_Page_Mvc(array(
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
        $this->assertEquals(5, $addedPages);
        
        $afterPage1 = $addedPages[0];
        $afterPage2 = $addedPages[1];
        $afterPage3 = $addedPages[2];
        $afterPage4 = $addedPages[3];
        $afterPage5 = $addedPages[4];
        
        $this->assertEquals($explicitVisibleZendNavPageMvc, $afterPage1);
        $this->assertNotEquals($implicitVisibleZendNavPageUri, $afterPage2);
        $this->assertNotEquals($notVisibleNavPageUriArray, $afterPage3);
        $this->assertEquals($notVisibleOmekaNavPageUri, $afterPage4);
        $this->assertEquals($explicitVisibleOmekaNavPageUri, $afterPage5);

        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage2);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage3);

        $this->assertEquals(0, $afterPage1->getOrder());
        $this->assertEquals(1, $afterPage2->getOrder());
        $this->assertEquals(2, $afterPage3->getOrder());
        $this->assertEquals(3, $afterPage4->getOrder());
        $this->assertEquals(4, $afterPage5->getOrder());
        
        $this->assertEquals(url('items/browse'), $afterPage1->getUid());
        $this->assertEquals(url('collections/browse'), $afterPage2->getUid());
        $this->assertEquals(url('items/edit'), $afterPage3->getUid());
        $this->assertEquals(url('collections/edit'), $afterPage4->getUid());
        $this->assertEquals('http://omeka.org', $afterPage5->getUid());

        $this->assertEquals(__('Browse Items'), $afterPage1->getLabel());
        $this->assertEquals(__('Browse Collections'), $afterPage2->getLabel());
        $this->assertEquals(__('Edit Items'), $afterPage3->getLabel());
        $this->assertEquals(__('Edit Collections'), $afterPage4->getLabel());
        $this->assertEquals(__('Omeka'), $afterPage5->getLabel());
    }
    
    public function addDifferentPagesNestedArray()
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
        
        $explicitVisibleZendNavPageMvc = new Zend_Navigation_Page_Mvc(array(
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
        
        $explicitVisibleOmekaNavPageUri = new Omeka_Navigation_Page_Uri(array(
            'label' => __('Omeka'),
            'uri' => 'http://omeka.org',
            'visible' => true
        ));
        
        $pages = array(
            $explicitVisibleZendNavPageMvc,
            $notVisibleOmekaNavPageUri,
            $explicitVisibleOmekaNavPageUri
        );
        
        $this->_nav->addPages($pages);
        
        $this->assertEquals(3, $this->_nav->count());
        $this->assertTrue($this->_nav->hasChildren());
        $this->assertTrue($this->_nav->hasPages());
        
        $addedPages = $this->_nav->getPages();
        $this->assertEquals(3, $addedPages);
        
        $afterPage1 = $addedPages[0];
        $afterPage2 = $addedPages[1];
        $afterPage3 = $addedPages[2];
        
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

        $afterPage1Page1 = $afterPage1Pages[0];
        $afterPage1Page2 = $afterPage1Pages[1];
        
        $this->assertEquals(1, $afterPage1Page1->count());
        $this->assertTrue($afterPage1Page1->hasChildren());
        $this->assertTrue($afterPage1Page1->hasPages());
        $this->assertEquals(0, $afterPage1Page2->count());
        $this->assertFalse($afterPage1Page2->hasChildren());
        $this->assertFalse($afterPage1Page2->hasPages());
        
        $afterPage1Page1Pages = $afterPage1Page1->getPages();
        $afterPage1Page1Page1 = $afterPage1Page1Pages[0];
        
        $this->assertEquals(0, $afterPage1Page1Page1->count());
        $this->assertFalse($afterPage1Page1Page1->hasChildren());
        $this->assertFalse($afterPage1Page1Page1->hasPages());
        
        $this->assertEquals($explicitVisibleZendNavPageMvc, $afterPage1);
        $this->assertNotEquals($implicitVisibleZendNavPageUri, $afterPage1Page1);
        $this->assertNotEquals($explicitVisibleOmekaNavPageUri, $afterPage1Page1Page1);
        $this->assertNotEquals($notVisibleNavPageUriArray, $afterPage1Page2);
        $this->assertEquals($notVisibleOmekaNavPageUri, $afterPage2);
        $this->assertEquals($explicitVisibleOmekaNavPageUri, $afterPage3);

        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage1Page1);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage1Page1Page1);
        $this->assertInstanceOf('Omeka_Navigation_Page_Uri', $afterPage1Page2);

        $this->assertEquals(0, $afterPage1->getOrder());
        $this->assertEquals(0, $afterPage1Page1->getOrder());
        $this->assertEquals(0, $afterPage1Page1Page1->getOrder());
        $this->assertEquals(1, $afterPage1Page2->getOrder());
        $this->assertEquals(1, $afterPage2->getOrder());
        $this->assertEquals(2, $afterPage3->getOrder());
        
        $this->assertEquals(url('items/browse'), $afterPage1->getUid());
        $this->assertEquals(url('collections/browse'), $afterPage1Page1->getUid());
        $this->assertEquals('http://chnm.gmu.edu', $afterPage1Page1Page1->getUid());
        $this->assertEquals(url('items/edit'), $afterPage1Page2->getUid());
        $this->assertEquals(url('collections/edit'), $afterPage2->getUid());
        $this->assertEquals('http://omeka.org', $afterPage3->getUid());

        $this->assertEquals(__('Browse Items'), $afterPage1->getLabel());
        $this->assertEquals(__('Browse Collections'), $afterPage1Page1->getLabel());
        $this->assertEquals(__('CHNM'), $afterPage1Page1Page1->getLabel());
        $this->assertEquals(__('Edit Items'), $afterPage1Page2->getLabel());
        $this->assertEquals(__('Edit Collections'), $afterPage2->getLabel());
        $this->assertEquals(__('Omeka'), $afterPage3->getLabel());
    }
}
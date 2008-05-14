<?php

/**
* 
*/
class ViewHelpersTestCase extends UnitTestCase
{
    protected $view, $router;
    
    public function setUp()
    {
        Omeka_Context::resetInstance();
        
        $router = Zend_Controller_Front::getInstance()->getRouter();
	    $router->addDefaultRoutes();
	    $view = new Omeka_View;
	    Zend_Registry::set('view', $view);
	    $this->view = $view;
	    $this->router = $router;
	    
	    require_once HELPERS;
    }
    
    public function testUrlHelperWorks()
    {
        $this->assertEqual('/items', $this->view->url(array('controller'=>'items')) );
        $this->assertEqual('/items', $this->view->url('items'));
    }
    
    public function testLinkToCanLinkToRecords()
    {
        //Configure the 'id' route
        $this->router->addRoute('id', new Zend_Controller_Router_Route(':controller/:action/:id'));
        
        //Configure a mock database connection so that the Item doesn't die
        $db = new MockOmeka_Db;
        $item = new Item($db);
        $item->id = 1;
        
        $this->assertEqual('<a href="/items/show/1" title="View Test">Test</a>', link_to($item, 'show', 'Test'));
    }
    
    public function testLinkToCanLinkWithoutARecord()
    {
        $this->assertEqual('<a href="/items" title="View Test">Test</a>', link_to('items', null, 'Test'));
    }
    
/* 	public function testUrlForCanGenerateProperUrls()
	{   
	    

	    require_once HELPERS;
	    $url = url_for(array('controller'=>'foo', 'action'=>'bar'));

	    $this->assertEqual("/foo/bar", $url);

	    $url = url_for('/foo/bar');

	    $this->assertEqual("/foo/bar", $url);
	} */
	
	
}

<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2013
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2013
 **/
class Omeka_Navigation_UriTest extends Omeka_Test_AppTestCase
{
    protected $_page;
    
    public function setUp()
    {
        parent::setUp();
        $this->_page = new Omeka_Navigation_Page_Uri();
    }
	
	public function testHrefHasParametersNoFragment()
	{
		$beforeHref = 'http://examplea.com/Omeka/items/browse?type=14';
		$afterHref = 'http://examplea.com/Omeka/items/browse?type=14';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = 'http://examplea.com/Omeka/items/browse?type=14&id=4';
		$afterHref = 'http://examplea.com/Omeka/items/browse?type=14&id=4';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = 'http://examplea.com/Omeka/items/browse?type=14&amp;id=4';
		$afterHref = 'http://examplea.com/Omeka/items/browse?type=14&amp;id=4';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$beforeHref = '/Omeka/items/browse?type=14';
		$afterHref = '/Omeka/items/browse?type=14';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = '/Omeka/items/browse?type=14&id=4';
		$afterHref = '/Omeka/items/browse?type=14&id=4';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = '/Omeka/items/browse?type=14&amp;id=4';
		$afterHref = '/Omeka/items/browse?type=14&amp;id=4';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
	}

	public function testHrefHasParametersHasFragment()
	{
		$beforeHref = 'http://examplea.com/Omeka/items/browse?type=14#item';
		$afterHref = 'http://examplea.com/Omeka/items/browse?type=14#item';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = 'http://examplea.com/Omeka/items/browse?type=14&id=4#item';
		$afterHref = 'http://examplea.com/Omeka/items/browse?type=14&id=4#item';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = 'http://examplea.com/Omeka/items/browse?type=14&amp;id=4#item';
		$afterHref = 'http://examplea.com/Omeka/items/browse?type=14&amp;id=4#item';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$beforeHref = '/Omeka/items/browse?type=14#item';
		$afterHref = '/Omeka/items/browse?type=14#item';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = '/Omeka/items/browse?type=14&id=4#item';
		$afterHref = '/Omeka/items/browse?type=14&id=4#item';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$this->_page = new Omeka_Navigation_Page_Uri();
		$beforeHref = '/Omeka/items/browse?type=14&amp;id=4#item';
		$afterHref = '/Omeka/items/browse?type=14&amp;id=4#item';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
	}
	
	public function testHrefNoParametersHasFragment()
	{
		$beforeHref = 'http://examplea.com/Omeka/items/browse#body';
		$afterHref = 'http://examplea.com/Omeka/items/browse#body';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
		
		$beforeHref = '/Omeka/items/browse#body';
		$afterHref = '/Omeka/items/browse#body';
		$this->_page->setHref($beforeHref);
		$this->assertEquals($afterHref, $this->_page->getHref());
	}
}
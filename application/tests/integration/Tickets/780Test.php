<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once HELPER_DIR . DIRECTORY_SEPARATOR . 'all.php';

/**
 * @internal For some awful reason, Zend Framework doesn't let you dispatch()
 * more than once per test method, so these URL dispatching tests have to be
 * split into separate methods.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 **/
class Tickets_780Test extends Omeka_Test_AppTestCase
{   
    protected $_useAdminViews = false;
         
    public function testAutoDiscoveryLinkTagOnItemsShow()
    {
        $this->_dispatchAndAssert('items/show/1');
    }
    
    public function testAutoDiscoveryLinkTagOnItemsBrowse()
    {
        $this->_dispatchAndAssert('items/browse');
    }
    
    public function testAutoDiscoveryLinkTagOnHome()
    {
        $this->_dispatchAndAssert('/');
    }
    
    public function testAutoDiscoveryLinkTagOnCollectionsShow()
    {
        $this->_dispatchAndAssert('collections/show/1');
    }
    
    private function _dispatchAndAssert($url)
    {
        $this->dispatch($url, false);
        $output = auto_discovery_link_tag();
        $this->assertEquals('<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="/items/browse?output=rss2" />', $output);
    }
}

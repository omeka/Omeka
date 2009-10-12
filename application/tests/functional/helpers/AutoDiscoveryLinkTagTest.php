<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once HELPERS;

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Helper_AutoDiscoveryLinkTagTest extends Zend_Test_PHPUnit_ControllerTestCase
{   
    public function setUp()
    {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function appBootstrap()
    {
        $this->core = new Omeka_Core('testing', array(
            'config' => CONFIG_DIR . DIRECTORY_SEPARATOR . 'application.ini'));
        
        $this->setUpBootstrap($this->core->getBootstrap());
        $this->core->bootstrap();
    }
    
    public function setUpBootstrap($bootstrap)
    {
        $this->frontController->getRouter()->addDefaultRoutes();
    }
    
    public function testLinkTagEscapesUrl()
    {
        $this->dispatch('/');
        $_GET['cookies&cream'] = 'tasty&delicious';
        $html = auto_discovery_link_tag();
        $this->assertEquals('<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="/items/browse?cookies%26cream=tasty%26delicious&amp;output=rss2" />', $html);
    }
    
    public function testLinkTagAvoidsXssAttack()
    {        
        $this->dispatch('/items/browse/%22%3e%3cscript%3ealert(11639)%3c/script%3e');
        $this->assertEquals('<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="/items/browse/&quot;&gt;&lt;script&gt;alert(11639)&lt;/script%3E?output=rss2" />', 
                            auto_discovery_link_tag());
    } 
    
    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
        Omeka_Context::resetInstance();
        parent::tearDown();
    }
}

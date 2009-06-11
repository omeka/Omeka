<?php
require_once HELPER_DIR . DIRECTORY_SEPARATOR . 'all.php';

class Tickets_780Test extends Omeka_Controller_TestCase
{
    public function _setUpBootstrap($bootstrap)
    {
        $mockDbResource = $this->_getMockBootstrapResource('Db', $this->_getMockDbWithMockTables());
        $bootstrap->registerPluginResource($mockDbResource);
        
        $bootstrap->setOptions(array(
            'resources'=> array(
                'Config' => array(),
                'FrontController' => array(),
                'Acl' => array(),
                'Options' => array('options'=> array('public_theme'=>'default')),
                'Theme' => array('basePath'=>BASE_DIR . '/themes', 'webBasePath'=>WEB_ROOT . '/themes')
            )
        ));
    }
    
    public function testAutoDiscoveryLinkOnItemShowPageLinksToBrowsePageOutput()
    {
        $this->dispatch('items/show/1');
        $output = auto_discovery_link_tag();
        $this->assertEquals('<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="/items/browse?output=rss2" />', $output);
    }
}

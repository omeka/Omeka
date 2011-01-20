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
class Omeka_Helper_AutoDiscoveryLinkTagTest extends Omeka_Test_AppTestCase
{           
    protected $_useAdminViews = false;
    
    public function testLinkTagEscapesUrl()
    {
        $this->dispatch('/');
        $_GET['cookies&cream'] = 'tasty&delicious';
        $html = '<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="/items/browse?cookies%26cream=tasty%26delicious&amp;output=rss2" />';
		$html .= '<link rel="alternate" type="application/atom+xml" title="Omeka Atom Feed" href="/items/browse?cookies%26cream=tasty%26delicious&amp;output=atom" />';
        $this->assertContains($html, auto_discovery_link_tag());
    }
    
    public function testLinkTagAvoidsXssAttack()
    {        
        $this->dispatch('/items/browse/%22%3e%3cscript%3ealert(11639)%3c/script%3e');
		$html = '<link rel="alternate" type="application/rss+xml" title="Omeka RSS Feed" href="/items/browse/%22%3E%3Cscript%3Ealert%2811639%29%3C/script%3E?output=rss2" />';
		$html .= '<link rel="alternate" type="application/atom+xml" title="Omeka Atom Feed" href="/items/browse/%22%3E%3Cscript%3Ealert%2811639%29%3C/script%3E?output=atom" />';
        $this->assertContains($html, 
                            auto_discovery_link_tag());
    } 
}

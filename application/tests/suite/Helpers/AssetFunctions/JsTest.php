<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_Helper_JsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->view = new Omeka_View;
        Zend_Registry::set('view', $this->view);
        
        // Trick it into loading existing shared javascripts.
        $this->view->addAssetPath(VIEW_SCRIPTS_DIR, 'http://fake.local/path/to/omeka');
    }
    
    public function testOutputsScriptTagWithHref()
    {
        // Test with Contains to avoid matching issues with newlines.
        $this->assertContains('<script type="text/javascript" src="http://fake.local/path/to/omeka/javascripts/vendor/jquery.js" charset="utf-8"></script>',
                            js_tag('vendor/jquery'));
    }

    
    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
}

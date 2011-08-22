<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

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
        $this->assertContains('<script type="text/javascript" src="http://fake.local/path/to/omeka/javascripts/prototype.js" charset="utf-8"></script>', 
                            js('prototype'));                    
    }
    
    public function testDefaultLoadsListOfJavascripts()
    {
        $expectedHrefs = array(
            "http://fake.local/path/to/omeka/javascripts/jquery.js",
            "http://fake.local/path/to/omeka/javascripts/jquery-noconflict.js",
            "http://fake.local/path/to/omeka/javascripts/jquery-ui.js",
        );
        
        $html = js('default');
        foreach ($expectedHrefs as $href) {
            $this->assertContains('<script type="text/javascript" src="' . $href . '" charset="utf-8"></script>', $html);
        }        
    }

    public function testDefaultLoadsListOfJavascriptsWithPrototype()
    {
        Omeka_Context::getInstance()->setOptions(array('enable_prototype' => '1'));
        $expectedHrefs = array(
            "http://fake.local/path/to/omeka/javascripts/prototype.js",
            "http://fake.local/path/to/omeka/javascripts/prototype-extensions.js",
            "http://fake.local/path/to/omeka/javascripts/scriptaculous.js?load=effects,dragdrop",
            "http://fake.local/path/to/omeka/javascripts/jquery.js",
            "http://fake.local/path/to/omeka/javascripts/jquery-noconflict.js",
            "http://fake.local/path/to/omeka/javascripts/jquery-ui.js",
        );

        $html = js('default');
        foreach ($expectedHrefs as $href) {
            $this->assertContains('<script type="text/javascript" src="' . $href . '" charset="utf-8"></script>', $html);
        }
        Omeka_Context::resetInstance();
    }
    
    public function testLoadingSpecificScriptaculousLibraries()
    {
        $this->assertContains('<script type="text/javascript" src="http://fake.local/path/to/omeka/javascripts/scriptaculous.js?load=foo,bar" charset="utf-8"></script>',
                              js('scriptaculous', 'javascripts', array('foo', 'bar')));
    }
    
    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
}

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
class Omeka_Helper_JsTest extends Omeka_Test_AppTestCase
{
    
    public function setUp()
    {
        parent::setUp();
        $this->view = __v();
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
            "http://fake.local/path/to/omeka/javascripts/prototype.js",
            "http://fake.local/path/to/omeka/javascripts/prototype-extensions.js",
            "http://fake.local/path/to/omeka/javascripts/scriptaculous.js?load=effects,dragdrop",
            "http://fake.local/path/to/omeka/javascripts/search.js");
        
        $html = js('default');
        foreach ($expectedHrefs as $href) {
            $this->assertContains('<script type="text/javascript" src="' . $href . '" charset="utf-8"></script>', $html);
        }        
    }
    
    public function testLoadingSpecificScriptaculousLibraries()
    {
        $this->assertContains('<script type="text/javascript" src="http://fake.local/path/to/omeka/javascripts/scriptaculous.js?load=foo,bar" charset="utf-8"></script>',
                              js('scriptaculous', 'javascripts', array('foo', 'bar')));
    }
}

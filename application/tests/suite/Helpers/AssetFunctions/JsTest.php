<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_Helper_JsTest extends Omeka_Test_TestCase
{
    public function setUpLegacy()
    {
        $this->view = new Omeka_View;
        Zend_Registry::set('view', $this->view);

        // Trick it into loading existing shared javascripts.
        $this->view->addAssetPath(VIEW_SCRIPTS_DIR, '/path/to/omeka');
    }

    public function testOutputsScriptTagWithHrefAndDefaultVersion()
    {
        // Test with Contains to avoid matching issues with newlines.
        $this->assertStringContainsString('<script type="text/javascript" src="/path/to/omeka/javascripts/vendor/jquery.js?v='.OMEKA_VERSION.'" charset="utf-8"></script>',
                            $this->_getJsTag());
    }

    public function testOutputsScriptTagWithHrefAndSpecificVersion()
    {
        $version = '1.2x';
        // Test with Contains to avoid matching issues with newlines.
        $this->assertStringContainsString('<script type="text/javascript" src="/path/to/omeka/javascripts/vendor/jquery.js?v='.$version.'" charset="utf-8"></script>',
                            $this->_getJsTag($version));
    }

    public function testOutputsScriptTagWithHrefAndNoVersion()
    {
        // Test with Contains to avoid matching issues with newlines.
        $this->assertStringContainsString('<script type="text/javascript" src="/path/to/omeka/javascripts/vendor/jquery.js" charset="utf-8"></script>',
                            $this->_getJsTag(null));
    }

    private function _getJsTag($version = OMEKA_VERSION, $dir = 'javascripts')
    {
        // Returns the JS tag with specific version
        return js_tag('vendor/jquery', $dir, $version);
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
    }
}

<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests for the queue_js_file/head_js pair of helpers.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Helper_DisplayJsTest extends Omeka_Test_TestCase
{
    const ASSET_PATH_ROOT = '/omeka-test/asset-path';

    public $externalDefaults;
    public $internalDefaults;

    public function setUpLegacy()
    {
        // Load a view object to allow get_view() to work.
        $this->view = new Omeka_View;
        Zend_Registry::set('view', $this->view);

        // Trick it into loading existing shared javascripts.
        $this->view->addAssetPath(VIEW_SCRIPTS_DIR, self::ASSET_PATH_ROOT);

        $bootstrap = new Omeka_Test_Bootstrap;
        Zend_Registry::set('bootstrap', $bootstrap);
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
    }

    private function _getJsOutput($includeDefaults = true)
    {
        ob_start();
        echo head_js($includeDefaults);
        return ob_get_clean();
    }

    private function _assertScriptsIncluded($output, $scriptPaths)
    {
        $dom = new Zend_Dom_Query('<fake>' . $output . '</fake>');
        foreach ($scriptPaths as $scriptPath) {
            $result = $dom->queryXpath("//script[@type='text/javascript' and @src='$scriptPath']");
            $this->assertCount(1, $result, "Script tag for '$scriptPath' not found.");
        }
    }

    public function testWithNoScripts()
    {
        $this->assertEquals('', $this->_getJsOutput(false));
    }

    public function testQueueJsWithDefaultVersion()
    {
        queue_js_file(array('items-search', 'vendor/tinymce/tinymce.min'));

        $scripts = array(
            self::ASSET_PATH_ROOT . '/javascripts/items-search.js?v='.OMEKA_VERSION,
            self::ASSET_PATH_ROOT . '/javascripts/vendor/tinymce/tinymce.min.js?v='.OMEKA_VERSION
        );

        $this->_assertScriptsIncluded($this->_getJsOutput(), $scripts);
    }

    public function testQueueJsWithNoVersion()
    {
        queue_js_file(array('items-search', 'vendor/tinymce/tinymce.min'), 'javascripts', array(), null);

        $scripts = array(
            self::ASSET_PATH_ROOT . '/javascripts/items-search.js',
            self::ASSET_PATH_ROOT . '/javascripts/vendor/tinymce/tinymce.min.js'
        );

        $this->_assertScriptsIncluded($this->_getJsOutput(), $scripts);
    }

    public function testQueueJsString()
    {
        $script = 'Inline JS script.';
        queue_js_string($script);

        $matcher = array(
            'tag' => 'script',
            'attributes' => array(
                'type' => 'text/javascript'
            )
        );

        $output = $this->_getJsOutput(false);
        $dom = new Zend_Dom_Query('<fake>' . $output . '</fake>');
        $result = $dom->queryXpath("//script[@type='text/javascript']");

        $this->assertCount(1, $result, "Script tag for inline script not found.");
        $this->assertStringContainsString($script, $output);
    }

    public function testQueueJsConditional()
    {
        queue_js_file('items-search', 'javascripts', array('conditional' => 'lt IE 9'));

        $output = $this->_getJsOutput(false);

        $this->assertStringContainsString('<!--[if lt IE 9]>', $output);
    }

    public function testQueueJsStringConditional()
    {
        $script = 'Inline JS script.';
        queue_js_string($script, array('conditional' => 'lt IE 9'));

        $output = $this->_getJsOutput(false);

        $this->assertStringContainsString('<!--[if lt IE 9]>', $output);
        $this->assertStringContainsString($script, $output);
    }
}

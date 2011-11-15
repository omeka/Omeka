<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * Tests for the queue_css, queue_css_string and display_css helpers.
 *
 * @package Omeka
 */
class Omeka_Helper_DisplayCssTest extends PHPUnit_Framework_TestCase
{
    const ASSET_PATH_ROOT = 'http://omeka-test/asset-path';

    public function setUp()
    {
        // Load a view object to allow __v() to work.
        $this->view = new Omeka_View;
        Zend_Registry::set('view', $this->view);

        // Trick it into loading existing shared styles.
        $this->view->addAssetPath(VIEW_SCRIPTS_DIR, self::ASSET_PATH_ROOT);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    private function _getCssOutput() {
        ob_start();
        display_css();
        return ob_get_clean();
    }

    private function _assertStyleLink($output, $path, $media = null) {
        $matcher = array(
            'tag' => 'link',
            'attributes' => array(
                'type' => 'text/css',
                'href' => $path
            )
        );

        if ($media) {
            $matcher['attributes']['media'] = $media;
        }

        $this->assertTag($matcher, $output,
            "Link tag for '$path' not found.");
    }

    private function _assertStylesheets($output, $cssPaths) {
        foreach ($cssPaths as $path) {
            $this->_assertStyleLink($output, $path);
        }
    }

    public function testWithNoStyles()
    {
        $this->assertEquals('', $this->_getCssOutput());

    }

    public function testQueueCssSingle()
    {
        queue_css('style');

        $styles = array(
            self::ASSET_PATH_ROOT . '/css/style.css'
        );

        $this->_assertStylesheets($this->_getCssOutput(), $styles);
    }

    public function testQueueCssArray()
    {
        queue_css(array('style', 'jquery-ui'));

        $styles = array(
            self::ASSET_PATH_ROOT . '/css/style.css',
            self::ASSET_PATH_ROOT . '/css/jquery-ui.css'
        );

        $this->_assertStylesheets($this->_getCssOutput(), $styles);
    }

    public function testQueueCssWithMedia()
    {
        queue_css('style', 'screen');

        $path = self::ASSET_PATH_ROOT . '/css/style.css';

        $this->_assertStyleLink($this->_getCssOutput(), $path, 'screen');
    }

    public function testQueueCssString()
    {
        $style = 'Inline stylesheet.';
        queue_css_string($style, 'screen');

        $matcher = array(
            'tag' => 'style',
            'attributes' => array(
                'type' => 'text/css',
                'media' => 'screen'
            )
        );

        $output = $this->_getCssOutput();

        $this->assertTag($matcher, $output,
            "Style tag for inline stylesheet not found.");
        $this->assertContains($style, $output);
    }
}

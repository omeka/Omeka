<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests for the queue_css, queue_css_string and head_css helpers.
 *
 * @package Omeka
 */
class Omeka_Helper_DisplayCssTest extends Omeka_Test_TestCase
{
    const ASSET_PATH_ROOT = '/omeka-test/asset-path';

    public function setUpLegacy()
    {
        // Load a view object to allow get_view() to work.
        $this->view = new Omeka_View;
        Zend_Registry::set('view', $this->view);

        // Trick it into loading existing shared styles.
        $this->view->addAssetPath(VIEW_SCRIPTS_DIR, self::ASSET_PATH_ROOT);
    }

    public function tearDownLegacy()
    {
        Zend_Registry::_unsetInstance();
    }

    private function _getCssOutput()
    {
        ob_start();
        echo head_css();
        return ob_get_clean();
    }

    private function _assertStyleLink($output, $path, $media = null)
    {
        $dom = new Zend_Dom_Query('<fake>' . $output . '</fake>');
        $attribQuery = "@type='text/css' and @href='$path'";
        if ($media) {
            $attribQuery .= " and @media='$media'";
        }
        $result = $dom->queryXpath("//link[$attribQuery]");
        $this->assertCount(1, $result, "Link tag for '$path' not found.");
    }

    private function _assertStylesheets($output, $cssPaths)
    {
        foreach ($cssPaths as $path) {
            $this->_assertStyleLink($output, $path);
        }
    }

    public function testWithNoStyles()
    {
        $this->assertEquals('', $this->_getCssOutput());
    }

    public function testQueueCssSingleWithDefaultVersion()
    {
        queue_css_file('style');

        $styles = array(
            self::ASSET_PATH_ROOT . '/css/style.css?v='.OMEKA_VERSION
        );

        $this->_assertStylesheets($this->_getCssOutput(), $styles);
    }

    public function testQueueCssSingleWithSpecificVersion()
    {
        $version = '1.2x';
        queue_css_file('style', 'all', false, 'css', $version);

        $styles = array(
            self::ASSET_PATH_ROOT . '/css/style.css?v='.$version
        );

        $this->_assertStylesheets($this->_getCssOutput(), $styles);
    }

    public function testQueueCssSingleWithNoVersion()
    {
        queue_css_file('style', 'all', false, 'css', null);

        $styles = array(
            self::ASSET_PATH_ROOT . '/css/style.css'
        );

        $this->_assertStylesheets($this->_getCssOutput(), $styles);
    }

    public function testQueueCssArrayWithDefaultVersion()
    {
        queue_css_file(array('style', 'jquery-ui'));

        $styles = array(
            self::ASSET_PATH_ROOT . '/css/style.css?v='.OMEKA_VERSION,
            self::ASSET_PATH_ROOT . '/css/jquery-ui.css?v='.OMEKA_VERSION
        );

        $this->_assertStylesheets($this->_getCssOutput(), $styles);
    }

    public function testQueueCssArrayWithNoVersion()
    {
        queue_css_file(array('style', 'jquery-ui'), 'all', false, 'css', null);

        $styles = array(
            self::ASSET_PATH_ROOT . '/css/style.css',
            self::ASSET_PATH_ROOT . '/css/jquery-ui.css'
        );

        $this->_assertStylesheets($this->_getCssOutput(), $styles);
    }

    public function testQueueCssWithMediaAndDefaultVersion()
    {
        queue_css_file('style', 'screen');

        $path = self::ASSET_PATH_ROOT . '/css/style.css?v='.OMEKA_VERSION;

        $this->_assertStyleLink($this->_getCssOutput(), $path, 'screen');
    }

    public function testQueueCssWithMediaAndNoVersion()
    {
        queue_css_file('style', 'screen', false, 'css', null);

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

        $dom = new Zend_Dom_Query('<fake>' . $output . '</fake>');
        $result = $dom->queryXpath("//style[@type='text/css' and @media='screen']");
        $this->assertCount(1, $result, "Style tag for inline stylesheet not found.");
        $this->assertStringContainsString($style, $output);
    }
}

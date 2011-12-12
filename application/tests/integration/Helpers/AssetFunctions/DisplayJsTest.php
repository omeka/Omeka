<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once HELPERS;

/**
 * Tests for the queue_js/display_js pair of helpers.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Helper_DisplayJsTest extends PHPUnit_Framework_TestCase
{
    const ASSET_PATH_ROOT = 'http://omeka-test/asset-path';

    public $externalDefaults;
    public $internalDefaults;
    public $prototypeScripts;

    public function setUp()
    { 
        $this->externalDefaults = array(
            'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
            'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js'
        );
        $this->internalDefaults = array(
            self::ASSET_PATH_ROOT . '/javascripts/jquery.js',
            self::ASSET_PATH_ROOT . '/javascripts/jquery-ui.js',
        );

        $this->prototypeScripts = array(
            self::ASSET_PATH_ROOT . '/javascripts/prototype.js',
            self::ASSET_PATH_ROOT . '/javascripts/prototype-extensions.js',
            self::ASSET_PATH_ROOT . '/javascripts/scriptaculous.js?load=effects,dragdrop'
        );
        
        // Load a view object to allow __v() to work.
        $this->view = new Omeka_View;
        Zend_Registry::set('view', $this->view);
        
        // Trick it into loading existing shared javascripts.
        $this->view->addAssetPath(VIEW_SCRIPTS_DIR, self::ASSET_PATH_ROOT);
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
    
    private function _getJsOutput($includeDefaults = true) {
        ob_start();
        display_js($includeDefaults);
        return ob_get_clean();
    }

    private function _assertScriptsIncluded($output, $scriptPaths) {
        foreach ($scriptPaths as $scriptPath) {
            $matcher = array(
                'tag' => 'script',
                'attributes' => array(
                    'type' => 'text/javascript',
                    'src' => $scriptPath
                )
            );
            $this->assertTag($matcher, $output, "Script tag for '$scriptPath' not found.");
        }
    }

    public function testWithNoScripts()
    {
        $this->assertEquals('', $this->_getJsOutput(false));
        
    }

    public function testDefaults()
    {
        $this->_assertScriptsIncluded($this->_getJsOutput(), $this->externalDefaults);
    }

    public function testInternalDefaults()
    {
        $configArray['theme']['useInternalJavascripts'] = true;
        Omeka_Context::getInstance()->setConfig('basic', new Zend_Config($configArray));

        $this->_assertScriptsIncluded($this->_getJsOutput(), $this->internalDefaults);
        Omeka_Context::resetInstance();
    }

    public function testPrototype()
    {
        Omeka_Context::getInstance()->setOptions(array('enable_prototype' => '1'));
        $this->assertEquals('', $this->_getJsOutput(false));

        $this->_assertScriptsIncluded($this->_getJsOutput(), $this->prototypeScripts);
        Omeka_Context::resetInstance();
    }

    public function testQueueJs()
    {
        queue_js(array('search', 'tiny_mce/tiny_mce'));

        $scripts = array(
            self::ASSET_PATH_ROOT . '/javascripts/search.js',
            self::ASSET_PATH_ROOT . '/javascripts/tiny_mce/tiny_mce.js'
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

        $this->assertTag($matcher, $output,
            "Script tag for inline script not found.");
        $this->assertContains($script, $output);
    }
}

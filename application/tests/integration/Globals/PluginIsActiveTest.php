<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Globals_PluginIsActiveTest extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'Foobar';

    public function setUp()
    {
        parent::setUp();
        $plugin = $this->db->getTable('Plugin')->find(1);
        if ($plugin) {
            $plugin->delete();
        }
        $plugin = new Plugin;
        $plugin->setDirectoryName(self::PLUGIN_NAME);
        $plugin->setActive(true);
        $plugin->setDbVersion('1.0');
        $plugin->forceSave();
        $this->plugin = $plugin;
        self::dbChanged(false);
    }

    public function testActive()
    {
        $this->assertTrue(plugin_is_active(self::PLUGIN_NAME));
    }

    public function testNotActive()
    {
        $this->plugin->setActive(false);
        $this->plugin->forceSave();
        $this->assertFalse(plugin_is_active(self::PLUGIN_NAME));
    }

    public static function versions()
    {
        return array(
            array('1.0', '>=', true),
            array('1.1', '>', false),
            array('0.9', '>=', true),
            array('1.0', '=', true),
            array('1.1', null, false), // Default should be false
            array('0.9', null, true),
            array('1.0', null, true),
        );
    }

    /**
     * @dataProvider versions
     */
    public function testVersionCheck($version, $compOperator, $isActive)
    {
        $method = $isActive ? 'assertTrue' : 'assertFalse';
        if ($compOperator) {
            $this->$method(plugin_is_active(self::PLUGIN_NAME, $version, $compOperator));
        } else {
            $this->$method(plugin_is_active(self::PLUGIN_NAME, $version));
        }
    }

    public function testNotInstalled()
    {
        $this->plugin->delete();
        $this->assertFalse(plugin_is_active(self::PLUGIN_NAME));
    }

    public static function tearDownAfterClass()
    {
        self::dbChanged(true);
    }
}

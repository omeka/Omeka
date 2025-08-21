<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Globals_PluginIsActiveTest extends Omeka_Test_AppTestCase
{
    const PLUGIN_NAME = 'Foobar';

    private $plugin;

    public function setUpLegacy()
    {
        parent::setUpLegacy();
        $plugin = $this->db->getTable('Plugin')->find(1);
        if ($plugin) {
            $plugin->delete();
        }
        $plugin = new Plugin;
        $plugin->setDirectoryName(self::PLUGIN_NAME);
        $plugin->setActive(true);
        $plugin->setDbVersion('1.0');
        $plugin->save();
        $this->plugin = $plugin;
    }

    public function testActive()
    {
        $this->assertTrue(plugin_is_active(self::PLUGIN_NAME));
    }

    public function testNotActive()
    {
        $this->plugin->setActive(false);
        $this->plugin->save();
        $this->assertFalse(plugin_is_active(self::PLUGIN_NAME));
    }

    public static function versions()
    {
        return [
            ['1.0', '>=', true],
            ['1.1', '>', false],
            ['0.9', '>=', true],
            ['1.0', '=', true],
            ['1.1', null, false], // Default should be false
            ['0.9', null, true],
            ['1.0', null, true],
        ];
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
}

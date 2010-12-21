<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Test set_theme_option().
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Globals_PluginIsActiveTest extends Omeka_Test_AppTestCase
{
    const ACTIVENAME = 'ActivePlugin';
    const INACTIVENAME = 'InactivePlugin';
    
    public function setUp()
    {
        parent::setUp();
        $activePlugin = new Plugin();
        $activePlugin->active = 1;
        $activePlugin->name = self::ACTIVENAME;
        $activePlugin->version = '1.0';
        $activePlugin->save();
        
        $inactivePlugin = new Plugin();
        $inactivePlugin->active = 0;
        $inactivePlugin->name = self::INACTIVENAME;
        $inactivePlugin->version = '1.0';
        $inactivePlugin->save();
    }
    
    public function testPluginActive()
    {
        $this->assertTrue(plugin_is_active(self::ACTIVENAME));
    }
    
    public function testPluginInactive()
    {
        $this->assertFalse(plugin_is_active(self::INACTIVENAME));
    }
    
    public function testNonExistingPlugin()
    {
        $this->assertFalse(plugin_is_active('NonExistingPlugin'));
    }
}
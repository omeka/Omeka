<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Plugin_InstallerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->broker = $this->getMock('Omeka_Plugin_Broker', array(), array(), '', false);
        $this->loader = $this->getMock('Omeka_Plugin_Loader', array(), array(), '', false);
        $this->iniReader = $this->getMock('Omeka_Plugin_Ini', array(), array(), '', false);
        $this->installer = new Omeka_Plugin_Installer($this->broker, 
                                                      $this->loader, 
                                                      $this->iniReader);
        // Plugin record with a mocked out database connection.                                              
        $this->plugin = $this->getMock('Plugin', array(), array(), '', false);      
        $this->plugin->id = 1;
        $this->plugin->name = 'foobar';                                              
    }
    
    public function testActivate()
    {
        $this->plugin->expects($this->once())
                 ->method('forceSave'); 
        
        $this->installer->activate($this->plugin);       
        $this->assertEquals(1, $this->plugin->active);
    }
    
    public function testDeactivate()
    {
        $this->plugin->expects($this->once())
                 ->method('forceSave'); 
        
        $this->installer->deactivate($this->plugin);       
        $this->assertEquals(0, $this->plugin->active);
    }
    
    public function testUpgrade()
    {
        // Convince us that there is something that can be upgraded.
        $this->loader->expects($this->once())
                 ->method('hasNewVersion')
                 ->will($this->returnValue(true));
        
        // Oh and it must have loaded successfully, somehow.
        $this->loader->expects($this->once())
                 ->method('isLoaded')
                 ->with('foobar')
                 ->will($this->returnValue(true));
        
        // Give it a fake hook (method on this test class) so we can assert 
        // whether it actually calls the 'upgrade' hook.
        $this->broker->expects($this->once())
                 ->method('getHook')
                 ->with('foobar', 'upgrade')
                 ->will($this->returnValue(array($this, 'upgradeHook')));
             
        $this->installer->upgrade($this->plugin);
        $this->assertTrue($this->upgradeHookFired);
    }
    
    public function testInstall()
    {        
        // Also, we loaded this thing successfully.
        $this->loader->expects($this->once())
                 ->method('load')
                 ->with('foobar', true);
        
        // Also convince us that the plugin meets the minimum version of Omeka.
        // $this->iniReader->expects($this->once())
        //          ->method('meetsOmekaMinimumVersion')
        //          ->with('foobar')
        //          ->will($this->returnValue(true));
        
        $this->broker->expects($this->once())
                 ->method('getHook')
                 ->with('foobar', 'install')
                 ->will($this->returnValue(array($this, 'installHook')));
        
        $this->plugin->expects($this->once())
                 ->method('forceSave');
        
        $this->installer->install($this->plugin);
        $this->assertTrue($this->installHookFired);
    }
    
    public function testUninstall()
    {
        // Trick us into thinking the plugin loaded successfully.
        $this->loader->expects($this->once())
                 ->method('isLoaded')
                 ->with('foobar')
                 ->will($this->returnValue(true));
        
        $this->broker->expects($this->once())
                 ->method('getHook')
                 ->with('foobar', 'uninstall')
                 ->will($this->returnValue(array($this, 'uninstallHook')));
        
        // The plugin record should be deleted at the end of the process.
        $this->plugin->expects($this->once())
                 ->method('delete');
        
        $this->installer->uninstall($this->plugin);
        $this->assertTrue($this->uninstallHookFired);
    }
    
    public function upgradeHook($oldVersion, $newVersion)
    {
        $this->upgradeHookFired = true;
    }
    
    public function installHook()
    {
        $this->installHookFired = true;
    }
    
    public function uninstallHook()
    {
        $this->uninstallHookFired = true;
    }
}
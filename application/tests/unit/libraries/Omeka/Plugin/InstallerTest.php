<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
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
        $this->plugin->expects($this->any())
                 ->method('getDirectoryName')
                 ->will($this->returnValue('foobar'));
        $this->plugin->expects($this->any())
                 ->method('getDisplayName')
                 ->will($this->returnValue('Foobar Display Name'));                                             
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
        $this->plugin->expects($this->once())
                 ->method('hasNewVersion')
                 ->will($this->returnValue(true));
        
        // Oh and it must have loaded successfully, somehow.
        $this->loader->expects($this->once())
                 ->method('load')
                 ->with($this->isInstanceOf('Plugin'), true);
        
        $this->plugin->expects($this->any())
                 ->method('getDbVersion')
                 ->will($this->returnValue('1.0'));
        $this->plugin->expects($this->any())
                 ->method('getIniVersion')
                 ->will($this->returnValue('1.1'));
        
        // Give it a fake hook (method on this test class) so we can assert 
        // whether it actually calls the 'upgrade' hook.
        $this->broker->expects($this->once())
                 ->method('callHook')
                 ->with('upgrade', array('1.0', '1.1'), $this->isInstanceOf('Plugin'));
             
        $this->installer->upgrade($this->plugin);
    }
    
    public function testInstall()
    {        
        $this->plugin->expects($this->once())
                 ->method('forceSave');

        // Also, we loaded this thing successfully.
        $this->loader->expects($this->once())
                 ->method('load')
                 ->with($this->isInstanceOf('Plugin'), true);
                
        $this->broker->expects($this->once())
                 ->method('callHook')
                 ->with('install', array(1), $this->isInstanceOf('Plugin'));
        
        $this->installer->install($this->plugin);
    }
    
    public function testUninstallingPluginThatIsNotLoaded()
    {        
        $this->plugin->expects($this->once())
                 ->method('isLoaded')
                 ->will($this->returnValue(false));
        
        // Trick us into thinking the plugin loaded successfully.
        $this->loader->expects($this->once())
                 ->method('load')
                 ->with($this->isInstanceOf('Plugin'));
        
        $this->broker->expects($this->once())
              ->method('callHook')
              ->with('uninstall', array(), $this->isInstanceOf('Plugin'));
        
        // The plugin record should be deleted at the end of the process.
        $this->plugin->expects($this->once())
                 ->method('delete');
        
        $this->installer->uninstall($this->plugin);
    }
    
    public function testUninstallingPluginThatIsLoadedAndActivated()
    {
        $this->plugin->expects($this->any())
                 ->method('isLoaded')
                 ->will($this->returnValue(true));
        
        // This will skip loading the plugin.
        $this->loader->expects($this->never())
                 ->method('load');
        
        $this->broker->expects($this->once())
                ->method('callHook')
                ->with('uninstall', array(), $this->isInstanceOf('Plugin'));
        
        $this->installer->uninstall($this->plugin);                  
    }        
}

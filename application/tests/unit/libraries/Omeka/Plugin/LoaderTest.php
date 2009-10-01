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
class Omeka_Plugin_LoaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->broker = $this->getMock('Omeka_Plugin_Broker', array(), array(), '', false);
        $this->basePath = TEST_DIR . '/_files/unit/plugin-loader';
        $this->iniReader = $this->getMock('Omeka_Plugin_Ini', array(), array(), '', false);
        $this->mvc = $this->getMock('Omeka_Plugin_Mvc', array(), array(), '', false);
        $this->loader = new Omeka_Plugin_Loader($this->broker, 
                                                $this->iniReader,
                                                $this->mvc,
                                                $this->basePath);
    }
    
    public function assertPreConditions()
    {
        // $this->assertFalse($this->loader->isLoaded('foobar'), "'foobar' plugin must not be loaded.");
        // $this->assertFalse($this->loader->isActive('foobar'), "'foobar' plugin must not be active.");
        // $this->assertTrue($this->loader->hasPluginFile('foobar'), "'plugin.php' file must exist at the following path: '$this->basePath/foobar/plugin.php'");
    }
    
    public function testLoadAllPlugins()
    {
        $pluginFoobar = new Plugin;
        $pluginFoobar->setDirectoryName('foobar');
        $pluginFoobar->setActive(true);
        $pluginBar = new Plugin;
        $pluginBar->setDirectoryName('Bar');
        $pluginBar->setActive(false);
                
        $this->loader->loadPlugins(array($pluginFoobar, $pluginBar));
        
        $this->assertTrue($pluginFoobar->isLoaded(), "'foobar' plugin should be loaded.");
        $this->assertFalse($pluginBar->isLoaded(), "'Bar' plugin should not be loaded.");
    }
    
    public function testLoadSpecificPlugin()
    {   
        $plugin = new Plugin;
        $plugin->setDirectoryName('foobar');
        $plugin->setActive(true);
        $plugin->setInstalled(true);
        $this->assertFalse($plugin->hasNewVersion());
        // It meets the Omeka minimum version if it hasn't set one.
        $plugin->setMinimumOmekaVersion(null);
        
        $this->mvc->expects($this->once())
                 ->method('addApplicationDirs')
                 ->with('foobar');
                
        $this->loader->load($plugin, true);
    }
    
    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
}

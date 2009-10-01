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
        
        $this->pluginFoobar = new Plugin;
        // Trick the record into thinking that it is installed by setting the ID.
        $this->pluginFoobar->id = 1;
        $this->pluginFoobar->setDirectoryName('foobar');
        $this->pluginFoobar->setActive(true);    
        
        // The NotActivatedPlugin
        $this->notActivatedPlugin = new Plugin;
        $this->notActivatedPlugin->id = 2;
        $this->notActivatedPlugin->setDirectoryName('NotActivatedPlugin');
        $this->notActivatedPlugin->setActive(false);                        
    }
        
    public function testLoadAllPlugins()
    {
        $this->loader->loadPlugins(array($this->pluginFoobar, $this->notActivatedPlugin));
        $this->assertTrue($this->pluginFoobar->isLoaded(), "'foobar' plugin should be loaded.");
        $this->assertFalse($this->notActivatedPlugin->isLoaded(), "'Bar' plugin should not be loaded.");
    }
    
    public function testLoadPlugin()
    {           
        $this->mvc->expects($this->once())
                 ->method('addApplicationDirs')
                 ->with('foobar');
        
        // Here we use the registry to store an instance of this test case.
        // By doing that, we can access the test case in the plugin.php 
        // bootstrap and set properties on it.
        // Zend_Registry::set('plugin_loader_test_case', $this);
        //         
        $this->loader->load($this->pluginFoobar, true);
        // $this->assertTrue($this->foobarPluginLoaded);
    }
    
    public function testLoadPluginThatDependsOnNonExistentPlugin()
    {
        $this->pluginFoobar->setRequiredPlugins(array('NonExistentPlugin'));
        try {
            $this->loader->load($this->pluginFoobar, true);
            $this->fail("Should have thrown an exception when could not load 'NonExistentPlugin'.");
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->assertContains("'NonExistentPlugin' required plugin could not be found.", $e->getMessage());
        }
    }
    
    public function testLoadPluginThatDependsOnNotActivatedPlugin()
    {
        $this->pluginFoobar->setRequiredPlugins(array('NotActivatedPlugin'));
        
        try {
            $this->loader->loadPlugins(array($this->pluginFoobar, $this->notActivatedPlugin), true);
            $this->fail("Should have thrown an exception when could not load 'NotActivatedPlugin'.");
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->assertContains("'NotActivatedPlugin' has not been activated.", $e->getMessage());
        }
    }
    
    public function testLoadPluginThatDependsOnAlreadyLoadedPlugin()
    {
        // $this->pluginFoobar->setRequiredPlugins(array(''))
    }
        
    public function testLoadPluginsWithCircularDependencies()
    {
        $this->circularDependencyPlugin = new Plugin;
        $this->circularDependencyPlugin->id = 3;
        $this->circularDependencyPlugin->setDirectoryName('CircularDependencyPlugin');
        $this->circularDependencyPlugin->setActive(true);
        $this->circularDependencyPlugin->setRequiredPlugins(array('foobar'));
        
        $this->pluginFoobar->setRequiredPlugins(array('CircularDependencyPlugin'));
        
        $this->loader->loadPlugins(array($this->pluginFoobar, $this->circularDependencyPlugin), true);
        $this->assertFalse($this->pluginFoobar->isLoaded(), "'foobar' plugin should not have been loaded.");
        $this->assertFalse($this->circularDependencyPlugin->isLoaded(), "'CircularDependencyPlugin' should not have been loaded.");
    }
}

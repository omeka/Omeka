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
        $this->db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $this->pluginFoobar = new Plugin($this->db);
        // Trick the record into thinking that it is installed by setting the ID.
        $this->pluginFoobar->id = 1;
        $this->pluginFoobar->setDirectoryName('foobar');
        $this->pluginFoobar->setActive(true);    
        
        // The NotActivatedPlugin
        $this->notActivatedPlugin = new Plugin($this->db);
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
        
        $this->assertFalse($this->pluginFoobar->isLoaded(), "'foobar' plugin should not be loaded.");
        $this->loader->load($this->pluginFoobar, true);
        $this->assertTrue($this->pluginFoobar->isLoaded(), "'foobar' plugin should be loaded.");   
    }
    
    public function testLoadPluginThatDependsOnNonExistentPlugin()
    {
        $this->pluginFoobar->setRequiredPlugins(array('NonExistentPlugin'));
        try {
            $this->loader->load($this->pluginFoobar, true);
            $this->fail("Should have thrown an exception when could not load 'NonExistentPlugin'.");
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->assertContains("The required plugin 'NonExistentPlugin' could not be found.", $e->getMessage());
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
        
    public function testLoadPluginsWithCircularDependencies()
    {
        $this->circularDependencyPlugin = new Plugin($this->db);
        $this->circularDependencyPlugin->id = 3;
        $this->circularDependencyPlugin->setDirectoryName('CircularDependencyPlugin');
        $this->circularDependencyPlugin->setActive(true);
        $this->circularDependencyPlugin->setRequiredPlugins(array('foobar'));
        
        $this->pluginFoobar->setRequiredPlugins(array('CircularDependencyPlugin'));
        
        $this->loader->loadPlugins(array($this->pluginFoobar, $this->circularDependencyPlugin), true);
        $this->assertFalse($this->pluginFoobar->isLoaded(), "'foobar' plugin should not have been loaded.");
        $this->assertFalse($this->circularDependencyPlugin->isLoaded(), "'CircularDependencyPlugin' should not have been loaded.");
    }
    
    public function testLoadPluginThatDependsOnAlreadyLoadedPlugin()
    {
        $this->pluginFoobar->setRequiredPlugins(array('AllPurposePlugin'));
        
        $this->alreadyLoadedPlugin = new Plugin($this->db);
        $this->alreadyLoadedPlugin->id = 4;
        $this->alreadyLoadedPlugin->setDirectoryName('AllPurposePlugin');
        $this->alreadyLoadedPlugin->setActive(true);
        
        $this->loader->load($this->alreadyLoadedPlugin, true);
        $this->loader->load($this->pluginFoobar, true);
    }
    
    public function testLoadPluginThatHasAnUpgradeAvailable()
    {
        $this->pluginFoobar->setIniVersion('1.0');
        $this->pluginFoobar->setDbVersion('0.1');
        
        try {
            $this->loader->load($this->pluginFoobar, true);
            $this->fail("Should have thrown an exception when attempting to load a plugin that has an available upgrade.");
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->assertContains("'foobar' must be upgraded to the new version before it can be loaded.", $e->getMessage());
        }
    }
    
    public function testLoadPluginThatHasAnOptionalNotActivatedPluginUsingLoadPlugins()
    {
        $this->pluginFoobar->setOptionalPlugins(array('NotActivatedPlugin'));        
        try {
            $this->loader->registerPlugin($this->notActivatedPlugin);
            $this->loader->loadPlugins(array($this->pluginFoobar), true);
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->fail("Should not have thrown an exception when it could not load the optional 'NotActivatedPlugin'.");
        }
    }
    
    public function testLoadPluginThatHasAnOptionalNotActivatedPluginUsingLoad()
    {
        $this->pluginFoobar->setOptionalPlugins(array('NotActivatedPlugin'));        
        try {
            $this->loader->registerPlugin($this->notActivatedPlugin);
            $this->loader->load($this->pluginFoobar, true);
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->fail("Should not have thrown an exception when it could not load the optional 'NotActivatedPlugin'.");
        }
    }
    
    public function testLoadedPluginIsRegistered()
    {
        try {
            $this->loader->loadPlugins(array($this->pluginFoobar), true);
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->fail("Should not have thrown an exception when it could not load 'foobar' plugin.");
        }
        
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");
    }
    
    public function testRegisteredPluginIsRegistered()
    {
        try {
            $this->loader->registerPlugin($this->pluginFoobar);
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->fail("Should not have thrown an exception when it registered the unregistered 'foobar' plugin.");
        }
        
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");
    }
    
    public function testUnregisteredPluginIsNotRegistered()
    {   
        $this->assertFalse($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should not be registered.");
    }
    
    public function testRegisterSamePluginObjectTwice()
    {
        try {
            $this->loader->registerPlugin($this->pluginFoobar);
        } catch(Omeka_Plugin_Loader_Exception $e) {
            $this->fail("Should not have thrown an exception when it tried to register the plugin.");
        }
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");
        
        try {
            $this->loader->registerPlugin($this->pluginFoobar);
        } catch(Omeka_Plugin_Loader_Exception $e) {
            $this->fail("Should not have thrown an exception when it tried to register same plugin object again.");
        }
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should still be registered after registering the same plugin object again.");
    }
    
    public function testRegisterTwoDifferentPluginObjectsWithSamePluginDirectories()
    {   
        $this->pluginWithSameDir = new Plugin($this->db);
        $this->pluginWithSameDir->id = 5;
        $this->pluginWithSameDir->setDirectoryName('foobar');
        $this->pluginWithSameDir->setActive(true);
        
        try {
            $this->loader->registerPlugin($this->pluginFoobar);
        } catch(Omeka_Plugin_Loader_Exception $e) {
            $this->fail("Should not have thrown an exception when it tried to register the plugin.");
        }
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");
        
        $hasException = false;    
        try {
            $this->loader->registerPlugin($this->pluginWithSameDir);
        } catch(Omeka_Plugin_Loader_Exception $e) {
            $hasException = true;
            $this->assertContains("Plugin named 'foobar' has already been loaded/registered.", $e->getMessage());
        }
        $this->assertTrue($hasException, "Should have thrown an exception when it tried to register another plugin object with the same directory.");
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should still be registered after attempting to register another plugin object with same directory.");
    }
        
    private function _printException($e) 
    {
        echo $e->getMessage() . "\n\n";
        echo $e->getTraceAsString() . "\n";
    }
}

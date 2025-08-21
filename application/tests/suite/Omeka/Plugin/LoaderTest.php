<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_Plugin_LoaderTest extends Omeka_Test_TestCase
{
    private $broker;
    private $basePath;
    private $iniReader;
    private $mvc;
    private $loader;
    private $db;
    private $pluginFoobar;
    private $notActivatedPlugin;

    public function setUpLegacy()
    {
        $this->broker = $this->getMock('Omeka_Plugin_Broker', [], [], '', false);
        $this->basePath = TEST_DIR . '/_files/unit/plugin-loader';
        $this->iniReader = $this->getMock('Omeka_Plugin_Ini', [], [], '', false);
        $this->mvc = $this->getMock('Omeka_Plugin_Mvc', [], [], '', false);
        $this->loader = new Omeka_Plugin_Loader($this->broker,
                                                $this->iniReader,
                                                $this->mvc,
                                                $this->basePath);
        $this->db = $this->getMock('Omeka_Db', [], [], '', false);
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
        $this->loader->loadPlugins([$this->pluginFoobar, $this->notActivatedPlugin]);
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
        $this->pluginFoobar->setRequiredPlugins(['NonExistentPlugin']);
        try {
            $this->loader->load($this->pluginFoobar, true);
            $this->fail("Should have thrown an exception when could not load 'NonExistentPlugin'.");
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->assertStringContainsString("The required plugin 'NonExistentPlugin' could not be found.", $e->getMessage());
        }
    }

    public function testLoadPluginThatDependsOnNotActivatedPlugin()
    {
        $this->pluginFoobar->setRequiredPlugins(['NotActivatedPlugin']);

        try {
            $this->loader->loadPlugins([$this->pluginFoobar, $this->notActivatedPlugin], true);
            $this->fail("Should have thrown an exception when could not load 'NotActivatedPlugin'.");
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->assertStringContainsString("'NotActivatedPlugin' has not been activated.", $e->getMessage());
        }
    }

    public function testLoadPluginsWithCircularDependencies()
    {
        $circularDependencyPlugin = new Plugin($this->db);
        $circularDependencyPlugin->id = 3;
        $circularDependencyPlugin->setDirectoryName('CircularDependencyPlugin');
        $circularDependencyPlugin->setActive(true);
        $circularDependencyPlugin->setRequiredPlugins(['foobar']);

        $this->pluginFoobar->setRequiredPlugins(['CircularDependencyPlugin']);

        $this->loader->loadPlugins([$this->pluginFoobar, $circularDependencyPlugin], true);
        $this->assertFalse($this->pluginFoobar->isLoaded(), "'foobar' plugin should not have been loaded.");
        $this->assertFalse($circularDependencyPlugin->isLoaded(), "'CircularDependencyPlugin' should not have been loaded.");
    }

    public function testLoadPluginThatDependsOnAlreadyLoadedPlugin()
    {
        $this->pluginFoobar->setRequiredPlugins(['AllPurposePlugin']);

        $alreadyLoadedPlugin = new Plugin($this->db);
        $alreadyLoadedPlugin->id = 4;
        $alreadyLoadedPlugin->setDirectoryName('AllPurposePlugin');
        $alreadyLoadedPlugin->setActive(true);

        $this->loader->load($alreadyLoadedPlugin, true);
        $this->loader->load($this->pluginFoobar, true);
        $this->assertTrue($alreadyLoadedPlugin->isLoaded());
        $this->assertTrue($this->pluginFoobar->isLoaded());
    }

    public function testLoadPluginThatHasAnUpgradeAvailable()
    {
        $this->pluginFoobar->setIniVersion('1.0');
        $this->pluginFoobar->setDbVersion('0.1');

        try {
            $this->loader->load($this->pluginFoobar, true);
            $this->fail("Should have thrown an exception when attempting to load a plugin that has an available upgrade.");
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $this->assertStringContainsString("'foobar' must be upgraded to the new version before it can be loaded.", $e->getMessage());
        }
    }

    public function testLoadPluginThatHasAnOptionalNotActivatedPluginUsingLoadPlugins()
    {
        $this->pluginFoobar->setOptionalPlugins(['NotActivatedPlugin']);
        $this->loader->registerPlugin($this->notActivatedPlugin);
        $this->loader->loadPlugins([$this->pluginFoobar], true);
        $this->assertTrue($this->pluginFoobar->isLoaded());
    }

    public function testLoadPluginThatHasAnOptionalNotActivatedPluginUsingLoad()
    {
        $this->pluginFoobar->setOptionalPlugins(['NotActivatedPlugin']);
        $this->loader->registerPlugin($this->notActivatedPlugin);
        $this->loader->load($this->pluginFoobar, true);
        $this->assertTrue($this->pluginFoobar->isLoaded());
    }

    public function testLoadedPluginIsRegistered()
    {
        $this->loader->loadPlugins([$this->pluginFoobar], true);
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");
    }

    public function testRegisteredPluginIsRegistered()
    {
        $this->loader->registerPlugin($this->pluginFoobar);
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");
    }

    public function testUnregisteredPluginIsNotRegistered()
    {
        $this->assertFalse($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should not be registered.");
    }

    public function testRegisterSamePluginObjectTwice()
    {
        $this->loader->registerPlugin($this->pluginFoobar);
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");

        $this->loader->registerPlugin($this->pluginFoobar);
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should still be registered after registering the same plugin object again.");
    }

    public function testRegisterTwoDifferentPluginObjectsWithSamePluginDirectories()
    {
        $pluginWithSameDir = new Plugin($this->db);
        $pluginWithSameDir->id = 5;
        $pluginWithSameDir->setDirectoryName('foobar');
        $pluginWithSameDir->setActive(true);

        $this->loader->registerPlugin($this->pluginFoobar);
        $this->assertTrue($this->loader->isRegistered($this->pluginFoobar), "'foobar' plugin should be registered.");

        $hasException = false;
        try {
            $this->loader->registerPlugin($pluginWithSameDir);
        } catch (Omeka_Plugin_Loader_Exception $e) {
            $hasException = true;
            $this->assertStringContainsString("Plugin named 'foobar' has already been loaded/registered.", $e->getMessage());
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

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
        $this->dbTable = $this->getMock('PluginTable', array(), array(), '', false);
        $this->basePath = TEST_DIR . '/_files/unit/plugin-loader';
        $this->iniReader = $this->getMock('Omeka_Plugin_Ini', array(), array(), '', false);
        $this->loader = new Omeka_Plugin_Loader($this->broker, 
                                                $this->dbTable,
                                                $this->iniReader,
                                                $this->basePath);
    }
    
    public function assertPreConditions()
    {
        $this->assertFalse($this->loader->isLoaded('foobar'), "'foobar' plugin must not be loaded.");
        $this->assertFalse($this->loader->isActive('foobar'), "'foobar' plugin must not be active.");
        $this->assertTrue($this->loader->hasPluginFile('foobar'), "'plugin.php' file must exist at the following path: '$this->basePath/foobar/plugin.php'");
    }
    
    public function testLoadingIteratesPluginRootDirectory()
    {
        $this->loader->loadLists();
    }
    
    public function testLoadSpecificPlugin()
    {   
        $this->assertFalse($this->loader->hasNewVersion('foobar'));
        
        $this->loader->setActive('foobar');     
        $this->loader->setInstalled('foobar');
    
        $this->iniReader->expects($this->any())
                 ->method('meetsOmekaMinimumVersion')
                 ->with('foobar')
                 ->will($this->returnValue(true));
        
        $this->loader->registerPluginBroker();
        
        $this->assertTrue($this->loader->canLoad('foobar'), "Loader is unable to load the 'foobar' plugin.");
        $this->loader->load('foobar');
    }
}

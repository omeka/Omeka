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
class Omeka_Plugin_IniTest extends PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        $this->basePath = TEST_DIR . '/_files/unit/plugin-loader';
        $this->iniReader = new Omeka_Plugin_Ini($this->basePath);
        $this->db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $this->plugin = new Plugin($this->db);
        $this->plugin->setDirectoryName('foobar');
    }
    
    public function assertPreConditions()
    {
        $gettersShouldBeNull = array(
            'getAuthor',
            'getDescription',
            'getMinimumOmekaVersion',
            'getTestedUpToOmekaVersion',
            'getLinkUrl',
            'getIniVersion'
        );
        
        foreach ($gettersShouldBeNull as $getterMethod) {
            $this->assertNull($this->plugin->$getterMethod(), "$getterMethod() should return null.");
        }
        
        $gettersShouldBeEmptyArray = array(
            'getRequiredPlugins',
            'getOptionalPlugins',
            'getIniTags'
        );
        
        foreach ($gettersShouldBeEmptyArray as $getterMethod) {
            $this->assertEquals(array(), $this->plugin->$getterMethod(), "$getterMethod() should return an empty array.");
        }
        
        $this->assertTrue($this->iniReader->hasPluginIniFile($this->plugin));
    }
    
    public function testLoadingIniDataIntoPluginRecord()
    {
        $this->iniReader->load($this->plugin);
        $this->assertEquals("Ini Reader Test Plugin", $this->plugin->getDisplayName());
        $this->assertEquals("Center for History & New Media", $this->plugin->getAuthor());
        $this->assertEquals("Tests the abilities of the IniReader class", $this->plugin->getDescription());
        $this->assertEquals("http://chnm.gmu.edu/", $this->plugin->getLinkUrl());
        $this->assertEquals("1.0beta", $this->plugin->getMinimumOmekaVersion());
        $this->assertEquals("1.0alpha", $this->plugin->getTestedUpToOmekaVersion());
        $this->assertEquals(array('Foo', 'Bar'), $this->plugin->getRequiredPlugins());
        $this->assertEquals(array('Baz', 'Dar'), $this->plugin->getOptionalPlugins());
        $this->assertEquals("1.0", $this->plugin->getIniVersion());
        $this->assertEquals(array('social', 'crucial'), $this->plugin->getIniTags());
    }
}

<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 * @todo The design needs to come together on this, so we don't have both a 
 * Context instance and a registry.  That is two service locators too many.  But
 * global state is required for global functions in the API.
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_GlobalFunctionsTest extends PHPUnit_Framework_TestCase
{
    public function testGetPluginIni()
    {
        $iniReader = $this->getMock('Omeka_Plugin_Ini', array(), array(), '', false);
        
        $iniReader->expects($this->once())
                 ->method('hasPluginIniFile')
                 ->with('foobar')
                 ->will($this->returnValue(true));
        $iniReader->expects($this->once())
                 ->method('getPluginIniValue')
                 ->with('foobar', 'foo')
                 ->will($this->returnValue('returned ini value'));

        Zend_Registry::set('plugin_ini_reader', $iniReader);
        
        $this->assertEquals('returned ini value', get_plugin_ini('foobar', 'foo'));
    }
    
    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }
}

<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Globals_GetPluginIniTest extends PHPUnit_Framework_TestCase
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

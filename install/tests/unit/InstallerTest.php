<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Installer.php';
require_once 'Installer/Requirements.php';

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class InstallerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->requirements = $this->getMock('Installer_Requirements');
        $this->db = $this->getMock('Omeka_Db', array(), array(), '', false);
        $this->installer = new Installer($this->requirements);
    }
    
    public function testCheckRequirements()
    {
        $this->requirements->expects($this->once())->method('check');
        $this->installer->checkRequirements();
    }
    
    public function testHasError()
    {
        // First call should return error messages.
        $this->requirements->expects($this->at(0))
            ->method('getErrorMessages')
            ->will($this->returnValue(array(
                array('header' => 'foo', 'message' => 'bar'))));
        
        // Second call should not.        
        $this->requirements->expects($this->at(1))
            ->method('getErrorMessages')
            ->will($this->returnValue(array()));
            
        $this->assertTrue($this->installer->hasError());
        $this->assertFalse($this->installer->hasError());
    }
    
    public function testHasWarning()
    {
        $this->requirements->expects($this->at(0))
            ->method('getWarningMessages')
            ->will($this->returnValue(array(
                array('header' => 'foo', 'message' => 'bar'))));
        
        // Second call should not.        
        $this->requirements->expects($this->at(1))
            ->method('getWarningMessages')
            ->will($this->returnValue(array()));
        
        $this->assertTrue($this->installer->hasWarning());
        $this->assertFalse($this->installer->hasWarning());
    }
}

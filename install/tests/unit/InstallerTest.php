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
        $this->db = $this->getMock('Omeka_Db', array('fetchAll', 'fetchOne', 'query'), array(), '', false);
        $this->installer = new Installer($this->db, $this->requirements);
    }
    
    public function testIsNotInstalledWithoutOptionsTable()
    {
        // Don't return any results for any query.  This should tell us that 
        // Omeka is not installed.
        $this->db->expects($this->any())->method('fetchAll')->will($this->returnValue(array()));        
        $this->assertFalse(Installer::isInstalled($this->db));
    }
    
    public function testIsNotInstalledWithEmptyOptionsTable()
    {
        // Convince the installer that there is an options table with nothing
        // in it.  Result = not installed.
        $this->db->expects($this->any())->method('fetchAll')->will($this->returnValue(array('options')));
        $this->db->expects($this->any())->method('fetchOne')->will($this->returnValue(0));
        
        $this->assertFalse(Installer::isInstalled($this->db));
    }
    
    public function testIsInstalledWithPopulatedOptionsTable()
    {
        // Convince the installer that there is an options table with stuff 
        // in it.  Result = installed.
        $this->db->expects($this->any())->method('fetchAll')->will($this->returnValue(array('options')));
        $this->db->expects($this->any())->method('fetchOne')->will($this->returnValue(10));
        
        $this->assertTrue(Installer::isInstalled($this->db), "Should be shown as installed when there is an 'options' table with data in it.");
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

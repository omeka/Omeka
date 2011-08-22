<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private 
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Controllers_SystemInfoControllerTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_authenticateUser($this->_getDefaultUser());
        self::dbChanged(false);
    }

    public static function tearDownAfterClass()
    {
        self::dbChanged(true);
    }

    public static function roles()
    {
        return array(
            array(null, false),
            array('researcher', false),
            array('contributor', false),
            array('admin', false),
            array('super', true),
        );
    }

    /**
     * @dataProvider roles
     */
    public function testAcl($role, $isAllowed)
    {
        $this->assertEquals($isAllowed, 
            $this->acl->isAllowed($role, 'SystemInfo', 'index'));
    }

    public function testDisabledSystemInfo()
    {
        set_option('display_system_info', false);
        $this->dispatch('system-info');
        $this->assertRedirectTo('/');
    }

    public function testDisplaySystemInfo()
    {
        // User agent never populated in CLI, workaround to prevent array 
        // index notice. 
        $_SERVER['HTTP_USER_AGENT'] = 'Omeka Test';
        set_option('display_system_info', true);
        $this->dispatch('system-info');
        $this->assertController('system-info');
        $this->assertAction('index');
    }

    public function testFooterLink()
    {
        set_option('display_system_info', true);
        $this->dispatch('/');
        $this->assertQuery("p#system-info a");
    }

    public function testDisabledFooterLink()
    {
        set_option('display_system_info', false);
        $this->dispatch('/');
        $this->assertNotQuery("p#system-info a");
    }

    public function testLinkPermissions()
    {
        set_option('display_system_info', true);
        $this->currentuser->role = 'admin';
        $this->dispatch('/');
        $this->assertNotQuery("p#system-info a");
    }

    public function testAccessPermissions()
    {
        set_option('display_system_info', true);
        $this->currentuser->role = 'admin';
        $this->dispatch('system-info');
        $this->assertNotController('system-info');
    }
}

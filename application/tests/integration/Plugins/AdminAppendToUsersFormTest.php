<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Plugins_AdminAppendToUsersFormTest extends Omeka_Test_AppTestCase
{
    protected $_isAdminTest = true;
    
    public function setUp()
    {
        parent::setUp();
        
        // Set the ACL to allow access to users.
        $this->acl = $this->core->getBootstrap()->acl;
        $this->acl->allow(null, 'Users');
        
        $this->db = $this->core->getBootstrap()->db;
        $this->user = $this->db->getTable('User')->find(1);
        $this->_authenticateUser($this->user);
    }
    
    public function assertPreConditions()
    {
        $this->assertTrue($this->acl->isAllowed($this->user, 'Users', 'edit'));
    }
    
    public function testCanAppendHtmlToAdminUsersForm()
    {
        add_plugin_hook('admin_append_to_users_form', array($this, 'appendExtraFormInput'));

        $this->dispatch('/users/edit/1', true);
        $this->assertNotRedirect();
        $this->assertContains("TEST HOOK CONTENT", $this->getResponse()->sendResponse());
        // var_dump($this->getResponse());exit;        
    }
    
    public function appendExtraFormInput()
    {
        echo "TEST HOOK CONTENT";
    }
}
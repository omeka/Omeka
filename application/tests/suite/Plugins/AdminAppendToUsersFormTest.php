<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests the hook for appending to the user edit form.
 *
 * @package Omeka
 */
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

        // Hack: add_plugin_hook() still doesn't allow arbitrary namespaces.
        $this->pluginbroker->setCurrentPluginDirName('__global__');
    }
    
    public function assertPreConditions()
    {
        $this->assertTrue($this->acl->isAllowed($this->user, 'Users', 'edit'));
    }
    
    public function testCanAppendHtmlToAdminUsersEditForm()
    {
        add_plugin_hook('admin_append_to_users_form', array($this, 'appendExtraFormInput'));

        $this->dispatch('/users/edit/1');
        $this->assertNotRedirect();
        $this->assertContains("TEST HOOK CONTENT", $this->getResponse()->sendResponse());
        // var_dump($this->getResponse());exit;        
    }
    
    public function testCanAppendHtmlToAdminUsersAddForm()
    {
        add_plugin_hook('admin_append_to_users_form', array($this, 'appendExtraFormInput'));
        $this->dispatch('/users/add');
        $this->assertNotRedirect();
        $this->assertQueryContentContains("label", "TEST HOOK CONTENT");
    }
    
    public function appendExtraFormInput(Omeka_Form_User $form)
    {
        $form->addElement('text', 'foobar', array('label' => 'TEST HOOK CONTENT'));
    }
}

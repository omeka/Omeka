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
class Omeka_Controllers_UsersFormTest extends Omeka_Test_AppTestCase
{    
    public function testAccessUserAccountInfo()
    {
        $this->_authenticateUser($this->_getDefaultUser());        
        // Super user.
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertController('users');
        $this->assertAction('edit', "Super users should be able to reach the 'edit' action for their user account.");
        $this->assertXpath('//input[@id="username"][@value="' . Omeka_Test_Resource_Db::SUPER_USERNAME . '"]',
            "There should be a 'username' element on this form with a default value.");            
        $this->assertXpath('//input[@id="first_name"][@value="' . Omeka_Form_Install::DEFAULT_USER_FIRST_NAME . '"]',
            "There should be a 'first_name' element on this form with a default value.");
        $this->assertXpath('//input[@id="last_name"][@value="' . Omeka_Form_Install::DEFAULT_USER_LAST_NAME . '"]',
            "There should be a 'last_name' element on this form with a default value.");
        $this->assertXpath('//input[@id="email"][@value="' . Omeka_Test_Resource_Db::SUPER_EMAIL . '"]',
            "There should be a 'email' element on this form with a default value.");
        $this->assertXpath('//input[@id="institution"][@value=""]',
            "There should be an 'institution' element on this form with no default value.");        
        $this->assertQuery("form select#role", "There should be a 'role' select on this form.");
        $this->assertQuery('form input[name="active"]', "There should be an 'active' element on this form.");
        
        $this->assertQuery('form input[type="submit"]', "There should be a submit button on this form.");
        
        // Admin user.
        $admin = $this->_addNewUserWithRole('admin');
        $this->_authenticateUser($admin);
        $this->dispatch('/users/edit/' . $admin->id);
        $this->assertController('users');
        $this->assertAction('edit', "Admin users should be able to reach the 'edit' action for their user account.");
        $this->assertQuery("form input#username", 
            "There should be a form with a 'username' element on it.");        
    }   
    
    public function testChangeOtherUsersAccountInfoAsSuperUser()
    {
        $this->_authenticateUser($this->_getDefaultUser());

        $admin = $this->_addNewUserWithRole('admin');
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'foobar2@example.com',
            'institution' => 'School of Hard Knocks',
            'role' => 'admin',
            'active' => '1'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $admin->id);
        $this->assertEquals($this->db->getTable('User')->find($admin->id)->username, "newusername");
        $this->assertRedirectTo('/users/browse');
    }
    
    public function testChangeOwnUserAccountInfo()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        
        $this->currentuser->role = 'admin';
        $this->currentuser->forceSave();
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'foobar@example.com',
            'institution' => 'School of Hard Knocks'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertEquals($this->_getDefaultUser()->username, "newusername");
        $this->assertRedirectTo('/');
    }

    public function testGivingInvalidEmailCausesValidationError()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'invalid.email',
            'institution' => 'School of Hard Knocks',
            'role' => 'super',
            'active' => '1'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertNotRedirect("This should not have redirected since the form submission was invalid.");
        // This error will be in a div in 1.x, 2.0 uses Zend_Form so it will be ul.errors.
        $this->assertQueryContentContains('div.error', "email address is not valid",
            "Form should contain an error message indicating that the email address provided was invalid.");
    }

    public function testCannotSetActiveFlagOrRoleFieldWithoutAdequatePermissions()
    {
        $adminUser = $this->_addNewUserWithRole('admin');
        $this->_authenticateUser($adminUser);        
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'foobar@example.com',
            'institution' => 'School of Hard Knocks',
            'role' => 'super',
            'active' => '0'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $adminUser->id);
        $newAdminUser = $this->db->getTable('User')->find($adminUser->id);
        $this->assertEquals($newAdminUser->role, 'admin', "User role should not have been changed from admin to super.");
        $this->assertEquals($newAdminUser->active, 1, "User status should not have been changed from active to inactive.");
    }
        
    public function testCannotEverChangeSaltPasswordOrEntityIdFields()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $oldSalt = $this->currentuser->salt;
        $oldHashedPassword = $this->currentuser->password;
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar',
            'last_name' => 'foobar',
            'email' => 'foobar@example.com',
            'institution' => 'School of Hard Knocks',
            'role' => 'super',
            'active' => '1',
            'entity_id' => '5000',
            'salt' => 'foobar',
            'password' => 'some-arbitrary-hash'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $changedUser = $this->_getDefaultUser();
        $this->assertEquals($changedUser->entity_id, 1, "Entity ID should not have changed.");
        $this->assertEquals($changedUser->salt, $oldSalt, "Salt should not have changed.");
        $this->assertEquals($changedUser->password, $oldHashedPassword, "Hashed password should not have changed.");
    }
        
    private function _addNewUserWithRole($role)
    {
        $newUser = new User;
        $newUser->username = 'newadminuser';
        $newUser->setPassword('foobar');
        $newUser->role = 'admin';
        $newUser->active = 1;
        $newUser->Entity = new Entity;
        $newUser->Entity->first_name = 'New';
        $newUser->Entity->last_name = 'Admin User';
        $newUser->Entity->email = 'bananabananabanana@example.com';
        $newUser->forceSave();
        $this->assertTrue($newUser->exists());
        return $newUser;
    }
}

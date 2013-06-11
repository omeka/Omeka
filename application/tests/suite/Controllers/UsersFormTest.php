<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controllers_UsersFormTest extends Omeka_Test_AppTestCase
{    
    public function setUp()
    {
        parent::setUp();
        $this->adminUser = $this->_addNewUserWithRole('admin');
        $this->superUser = $this->_addNewUserWithRole('super');
    }

    public function testSuperCanAccessForm()
    {
        $this->_authenticateUser($this->superUser);        
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertController('users');
        $this->assertAction('edit', "Super users should be able to reach the 'edit' action for their user account.");
    }

    public static function formXPaths()
    {
        return array(
            array('//input[@id="username"][@value="adminuser"]', 
                "There should be a 'username' element on this form with a default "
                . "value."),
            array(
                '//input[@id="name"][@value="Admin User"]',
                "There should be a 'name' element on this form with a default "
                . "value."),
            array(
                '//input[@id="email"][@value="admin@example.com"]',
                "There should be a 'email' element on this form with a default value.")
        );
    }

    public static function formQueries()
    {
        return array(
            array("form select#role", "There should be a 'role' select on this "
            . "form."),
            array('form input[name="active"]', "There should be an 'active' "
            . "element on this form."),
            array('form input[type="submit"]', "There should be a submit button on "
            . "this form."),
        );
    }

    /**
     * @dataProvider formXPaths
     */
    public function testFormXPath($xPath, $failMsg)
    {
        $this->_authenticateUser($this->superUser);        
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $this->assertXpath($xPath, $failMsg);
    }   

    /**
     * @dataProvider formQueries
     */
    public function testFormQuery($query, $failMsg)
    {
        $this->_authenticateUser($this->superUser);        
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $this->assertQuery($query, $failMsg);
    }
    
    public function testChangeOtherUsersAccountInfoAsSuperUser()
    {
        $expectedUsername = 'newuser' . mt_rand();
        $this->_authenticateUser($this->superUser);
        $this->request->setPost(array(
            'username' => $expectedUsername,
            'name' => 'foobar',
            'email' => 'admin' . mt_rand() . '@example.com',
            'role' => 'admin',
            'active' => '1'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $newUsername = $this->db->getTable('User')->find($this->adminUser->id)->username;
        $this->assertEquals($expectedUsername, $newUsername);
        $this->assertRedirectTo('/users/edit/' . $this->adminUser->id);
    }
    
    public function testChangeOwnUserAccountInfo()
    {
        $user = $this->superUser;
        $this->_authenticateUser($user);
        $this->request->setPost(array(
            'username' => 'newusername',
            'name' => 'foobar foobar',
            'email' => 'foobar' . mt_rand() . '@example.com',
            'active' => '1',
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertRedirectTo('/users/edit/' . $this->currentuser->id);
        $changedUser = $this->db->getTable('User')->find($user->id);
        $this->assertEquals("newusername", $changedUser->username);
    }

    public function testGivingInvalidEmailCausesValidationError()
    {
        $this->_authenticateUser($this->superUser);
        $this->request->setPost(array(
            'username' => 'newusername',
            'first_name' => 'foobar foobar',
            'email' => 'invalid.email',
            'role' => 'super',
            'active' => '1'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $this->assertNotRedirect("This should not have redirected since the form submission was invalid.");
        $this->assertQueryContentContains('ul.error', "email address is invalid",
            "Form should contain an error message indicating that the email address provided was invalid.");
    }

    public function testCannotSetActiveFlagOrRoleFieldWithoutAdequatePermissions()
    {
        $this->_authenticateUser($this->adminUser);        
        $this->request->setPost(array(
            'username' => 'newusername',
            'name' => 'foobar foobar',
            'email' => 'foobar@example.com',
            'role' => 'super',
            'active' => '0'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->adminUser->id);
        $newAdminUser = $this->db->getTable('User')->find($this->adminUser->id);
        $this->assertEquals($newAdminUser->role, 'admin', "User role should not have been changed from admin to super.");
        $this->assertEquals($newAdminUser->active, 1, "User status should not have been changed from active to inactive.");
    }
        
    public function testCannotEverChangeSaltOrPasswordFields()
    {
        $user = $this->adminUser;
        $this->_authenticateUser($user);
        $this->request->setPost(array(
            'username' => 'newusername',
            'name' => 'foobar foobar',
            'email' => 'foobar@example.com',
            'role' => 'super',
            'active' => '1',
            'salt' => 'foobar',
            'password' => 'some-arbitrary-hash'
        ));
        $this->request->setMethod('post');
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $changedUser = $this->db->getTable('User')->find($user->id);
        $this->assertEquals($user->salt, $changedUser->salt, 
            "Salt should not have changed.");
        $this->assertEquals($user->password, $changedUser->password, 
            "Hashed password should not have changed.");
    }
        
    private function _addNewUserWithRole($role)
    {
        $username = $role . 'user';
        $existingUser = $this->_getUser($username);
        if ($existingUser) {
            $existingUser->delete();
            release_object($existingUser);
        }
        $newUser = new User;
        $newUser->username = $username;
        $newUser->setPassword('foobar');
        $newUser->role = $role;
        $newUser->active = 1;
        $newUser->name = ucwords($role) . ' User';
        $newUser->email = $role . '@example.com';
        $newUser->save();
        return $newUser;
    }

    private function _getUser($username)
    {
        return $this->db->getTable('User')->findBySql("username = ?", array($username), true);
    }
}

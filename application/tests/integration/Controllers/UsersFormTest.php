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
        
        $basicTextInputs = array('username', 'first_name', 'last_name', 'email', 'institution');
        
        // Super user.
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertController('users');
        $this->assertAction('edit', "Super users should be able to reach the 'edit' action for their user account.");
        foreach ($basicTextInputs as $elementName) {
            $this->assertQuery("form input#$elementName", 
                "There should be a '$elementName' element on this form.");
        }
        $this->assertQuery("form select#role", "There should be a 'role' select on this form.");
        $this->assertQuery("form input#active", "There should be an 'active' element on this form.");
        
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
            'institution' => 'School of Hard Knocks'
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

<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 **/
class Omeka_Controller_UsersControllerTest extends Omeka_Test_AppTestCase
{
    
    public function setUp()
    {
        parent::setUp();

        $this->mailHelper = Omeka_Test_Helper_Mail::factory();        
        $this->email = Zend_Registry::get('test_config')->email->to;        
        $this->mailHelper->reset();
        
        $this->_authenticateUser($this->_getDefaultUser());
    }
    
    public function testAddingNewUserSendsActivationEmail()
    {
        $post = array('username'    => 'foobar',
                      'first_name'  => 'foobar',
                      'last_name'   => 'foobar',
                      'institution' => 'foobar',
                      'email'       => $this->email,
                      'role'        => 'admin');
        $this->getRequest()->setPost($post);
        $this->getRequest()->setMethod('post');
        $this->dispatch('users/add');
        $this->assertRedirectTo('/users/browse');
        $this->assertThat($this->mailHelper->getMailText(), $this->stringContains("Activate your account"));
    }
    
    public function testShowForgotPasswordPage()
    {
        $this->dispatch('users/forgot-password');
        $this->assertNotRedirect();
        $this->assertQuery('form #email');
    }
    
    public function testForgotPasswordForInvalidEmail()
    {
        $invalidEmail = 'bananabanana@example.com';
        $this->request->setPost(array(
            'email' => $invalidEmail
        ));
        $this->request->setMethod('post');
        $this->dispatch('users/forgot-password');
        $this->assertNotRedirect();
        $this->assertQueryContentContains("div.error", "email address", 
            "The form should have responded with an error message indicating that the email address was not found.");
    }
    
    public function testSendingEmailForForgottenPassword()
    {
        $this->request->setPost(array(
            'email' => Omeka_Test_Resource_Db::SUPER_EMAIL
        ));
        $this->request->setMethod('post');
        $this->dispatch('users/forgot-password');
        $mail = $this->mailHelper->getMailText();
        $this->assertThat($mail, $this->stringContains("Subject: [Automated Test Installation] Reset Your Password"));
        $this->assertQueryContentContains("div.success", "Please check your email");
        
        $activationCode = $this->db->fetchOne("SELECT url FROM omeka_users_activations LIMIT 1");
        $this->assertThat($mail, $this->stringContains($activationCode), 
            "Email should contain the activation code send to the user.");
    }
    
    public function testAccessUserAccountInfo()
    {
        // Super user.
        $this->dispatch('/users/edit/' . $this->currentuser->id);
        $this->assertController('users');
        $this->assertAction('edit', "Super users should be able to reach the 'edit' action for their user account.");
        $this->assertQuery("form input#username", 
            "There should be a form with a 'username' element on it.");
        
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
        $this->assertRedirectTo('/users/browse');
    }
    
    public function testChangeOwnUserAccountInfo()
    {
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
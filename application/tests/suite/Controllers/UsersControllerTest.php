<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Controller_UsersControllerTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mailHelper = Omeka_Test_Helper_Mail::factory();
        $this->email = 'user@example.com';
    }

    public function testAddingNewUserSendsActivationEmail()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $post = array('username' => 'foobar',
                      'name' => 'foobar',
                      'email' => $this->email,
                      'role' => 'admin',
                      'active' => '1',
                      'user_csrf' => $this->_getCsrfToken());
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
        $this->assertQueryContentContains("li.error", "Unable to reset password.",
            "The form should have responded with an error message indicating there was a problem.");
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
        $this->assertQueryContentContains("li.success", "Please check your email");

        $activationCode = $this->db->fetchOne("SELECT url FROM omeka_users_activations LIMIT 1");
        $this->assertThat($mail, $this->stringContains($activationCode),
            "Email should contain the activation code send to the user.");
        $this->db->query("TRUNCATE {$this->db->UsersActivations}");
    }

    public function testForgotPasswordForInactiveUser()
    {
        $user = $this->_getDefaultUser();
        $user->active = 0;
        $user->save();
        $inactiveEmail = $user->email;
        $this->request->setPost(array(
            'email' => $inactiveEmail
        ));
        $this->request->setMethod('post');
        $this->dispatch('users/forgot-password');
        $this->assertNotRedirect();
        $this->assertQueryContentContains("li.error", "Unable to reset password.",
            "The form should have responded with an error message indicating there was a problem.");
    }

    public function testEditOtherRedirect()
    {
        $this->_authenticateUser($this->_getDefaultUser());
        $userInfo = array(
            'name' => 'New User',
            'email' => $this->email,
            'role' => 'super',
            'username' => 'newuser'
        );

        $user = new User;
        $user->setPostData($userInfo);
        $user->save();

        $id = $user->id;

        $request = $this->getRequest();
        $request->setPost(array(
            'username' => 'differentuser',
            'name' => 'Different User',
            'email' => $this->email,
            'role' => 'super',
            'user_csrf' => $this->_getCsrfToken()
        ));
        $request->setMethod('post');
        $this->dispatch("users/edit/$id");
        $this->assertRedirectTo("/users/edit/$id");
    }

    public function testEditSelfRedirect()
    {
        $user = $this->_getDefaultUser();
        $this->_authenticateUser($user);
        $id = $user->id;

        $request = $this->getRequest();
        $request->setPost(array(
            'username' => 'differentuser',
            'name' => 'Different User',
            'email' => $this->email,
            'role' => 'super',
            'user_csrf' => $this->_getCsrfToken()
        ));
        $request->setMethod('post');
        $this->dispatch("users/edit/$id");
        $this->assertRedirectTo("/users/edit/$id");
    }

    private function _getCsrfToken()
    {
        $hash = new Zend_Form_Element_Hash('user_csrf');
        $hash->initCsrfToken();
        return $hash->getHash();
    }
}

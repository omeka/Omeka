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
                
        // Set the ACL to allow access to users.
        $this->acl->allow(null, 'Users');
        
        $testConfig = Zend_Registry::get('test_config');
        $this->email = $testConfig->email->to;
        
        // Verify and clear the fakemail directory prior to running the tests.
        $this->fakemail = $testConfig->paths->fakemaildir;
        if (!(is_dir($this->fakemail) && is_readable($this->fakemail))) {
            die("paths.fakemaildir must be properly configured in config.ini");
        }
        
        $iter = new VersionedDirectoryIterator($this->fakemail, false);
        foreach ($iter as $file) {
            assert(file_exists("$this->fakemail/$file"));
            unlink("$this->fakemail/$file");
        }        
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
        $this->dispatch('users/add', true);
        $mailText = $this->_getSentMailText();
        $this->assertThat($mailText, $this->stringContains("Activate your account"));
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
        $mail = $this->_getSentMailText();
        $this->assertThat($mail, $this->stringContains("Subject: [Automated Test Installation] Reset Your Password"));
        $this->assertQueryContentContains("div.success", "Please check your email");
        
        $activationCode = $this->db->fetchOne("SELECT url FROM omeka_users_activations LIMIT 1");
        $this->assertThat($mail, $this->stringContains($activationCode), 
            "Email should contain the activation code send to the user.");
    }
            
    private function _getFakemailFilenames()
    {
        $iter = new VersionedDirectoryIterator($this->fakemail, false);
        return $iter->getValid();
    }
    
    private function _getSentMailText()
    {
        $mail = $this->_getFakemailFilenames();
        $this->assertEquals(count($mail), 1);
        return file_get_contents("$this->fakemail/{$mail[0]}");
    }
}
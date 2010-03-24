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
        
        // Tack on the admin theme for use in view scripts.
        $this->view = Zend_Registry::get('view');
        $this->view->addScriptPath(ADMIN_THEME_DIR . DIRECTORY_SEPARATOR . 'default');
        
        // Set the ACL to allow access to users.
        $acl = $this->core->getBootstrap()->getResource('Acl');
        $acl->allow(null, 'Users');
        
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
        $mail = $this->_getFakemailFilenames();
        $this->assertEquals(count($mail), 1);
        $mailText = file_get_contents("$this->fakemail/{$mail[0]}");
        $this->assertThat($mailText, $this->stringContains("Activate your account"));
    }
    
    public function testForgotPasswordSendsEmail()
    {
        $this->getRequest()->setMethod('post');
        $this->getRequest()->setPost(array('email'=> $this->email));
    }    
    
    private function _getFakemailFilenames()
    {
        $iter = new VersionedDirectoryIterator($this->fakemail, false);
        return $iter->getValid();
    }
}
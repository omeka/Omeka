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
        
        $this->email = Zend_Registry::get('test_config')->email->to;
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
        $this->assertContains("Activation email sent to '{$this->email}'",
                              $this->_getLastLogLine(LOGS_DIR . '/errors.log'));
    }
    
    public function testForgotPasswordSendsEmail()
    {
        $this->getRequest()->setMethod('post');
        $this->getRequest()->setPost(array('email'=> $this->email));
    }
    
    private function _getLastLogLine($logFile)
    {
        $file = escapeshellarg($logFile); 
        return `tail -n 1 $file`;
    }
}
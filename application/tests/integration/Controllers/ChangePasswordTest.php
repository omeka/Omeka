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
class Omeka_Controllers_ChangePasswordTest extends Omeka_Test_AppTestCase
{
    const CURRENT_PASSWORD = 'foobar123';
    
    public function setUp()
    {
        parent::setUp();
                
        // Set the ACL to allow access to users.
        $acl = $this->core->getBootstrap()->acl;
        $acl->allow(null, 'Users');
                
        $this->db = $this->core->getBootstrap()->db;
        $this->user = $this->db->getTable('User')->find(1);
        $this->salt = $this->user->salt;
        
        // The user is attempting to change their own password.
        // Pretend that this user is not a super user.
        $this->user->role = 'admin';
        $this->core->getBootstrap()->currentUser = $this->user;
    }

    public function assertPreConditions()
    {
        $this->assertTrue($this->user instanceof User);
        $this->assertTrue($this->user->exists());
        $this->assertNotNull($this->salt, "Salt not being set properly by installer.");
        $this->_assertPasswordNotChanged();
    }
    
    public function assertPostConditions()
    {
        $this->_assertSaltNotChanged();
    }
    
    public function testChangingPassword()
    {
        $this->getRequest()->setPost(array(
            'old_password'  => self::CURRENT_PASSWORD,
            'new_password1' => 'foobar6789',
            'new_password2' => 'foobar6789'
        ));
        $this->getRequest()->setMethod('post');
        $this->dispatch('/users/change-password/1', true);
        $this->_assertPasswordIs('foobar6789');
    }
    
    public function testSuperUserCanChangePasswordWithoutKnowingOriginal()
    {
        $this->user->role = 'super';
        $this->getRequest()->setPost(array(
            'new_password1' => 'foobar6789',
            'new_password2' => 'foobar6789'
        ));
        $this->getRequest()->setMethod('post');
        $this->dispatch('/users/change-password/1', true);
        $this->_assertPasswordIs('foobar6789');
    }
    
    public function testChangingPasswordFailsWithInvalidPassword()
    {
        $this->getRequest()->setPost(array(
            'old_password'  => 'wrongpassword',
            'new_password1' => 'foo',
            'new_password2' => 'foo'
        ));
        $this->getRequest()->setMethod('post');
        $this->dispatch('/users/change-password/1', true);
        $this->_assertPasswordNotChanged();
    }
    
    public function testChangePasswordFailsIfPasswordNotConfirmed()
    {
        $this->getRequest()->setPost(array(
            'old_password'  => 'foobar123',
            'new_password1' => 'foo'
        ));
        $this->getRequest()->setMethod('post');
        $this->dispatch('/users/change-password/1', true);
        $this->_assertPasswordNotChanged();
    }
    
    private function _assertPasswordNotChanged()
    {
        $this->_assertPasswordIs(self::CURRENT_PASSWORD,
                                 "Hashed password should not have changed.");
    }
    
    private function _assertPasswordIs($pass, $msg = null)
    {
        $this->assertEquals($this->db->fetchOne("SELECT password FROM omeka_users WHERE id = 1"),
                            $this->user->hashPassword($pass),
                            $msg);
    }
    
    private function _assertSaltNotChanged()
    {
        $this->assertEquals($this->db->fetchOne("SELECT salt FROM omeka_users WHERE id = 1"),
                                                $this->salt,
                                                "Salt should not have changed.");
    }    
}
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
class Omeka_Controllers_ChangePasswordTest extends Omeka_Test_AppTestCase
{    
    const FORM_URL = '/users/edit/1';
    
    public function setUp()
    {
        parent::setUp();

        $this->user = $this->_getDefaultUser();
        $this->salt = $this->user->salt;
        
        // The user is attempting to change their own password.
        // Pretend that this user is not a super user.
        $this->_authenticateUser($this->user);
        $this->user->role = 'admin';
        $this->user->forceSave();
    }

    public function assertPreConditions()
    {
        $this->assertNotNull($this->salt, "Salt not being set properly by installer.");
        $this->_assertPasswordNotChanged();
    }
    
    public function assertPostConditions()
    {
        $this->_assertSaltNotChanged();
    }
    
    public function testChangePasswordFormAsAdminUser()
    {
        $this->dispatch(self::FORM_URL);
        $this->assertController('users');
        $this->assertAction('edit');
        $this->assertNotRedirect();
        $this->assertQuery('form#change-password input#current_password');
        $this->assertQuery('form#change-password input#new_password');
        $this->assertQuery('form#change-password input#new_password_confirm');
    }
    
    public function testChangePasswordFormAsSuperUser()
    {
        $this->user->role = 'super';
        $this->user->forceSave();
        $this->dispatch(self::FORM_URL);
        $this->assertNotRedirect();
        $this->assertNotQuery('form#change-password input#current_password');
        $this->assertQuery('form#change-password input#new_password');
        $this->assertQuery('form#change-password input#new_password_confirm');        
    }
    
    public function testAdminUserCannotChangePasswordForAnotherUser()
    {
        $newUser = $this->_addNewUserWithRole('contributor');
        $this->dispatch('/users/edit/' . $newUser->id);
        $this->assertNotController('users');
        $this->assertNotAction('edit');
    }
    
    public function testSuperUserCanChangePasswordForAnotherUser()
    {
        $this->user->role = 'super';
        $this->user->forceSave();
        $newUser = $this->_addNewUserWithRole('admin');
        $this->dispatch('/users/edit/' . $newUser->id);
        $this->assertController('users');
        $this->assertAction('edit');
    }
    
    public function testChangingPassword()
    {
        $this->_dispatchChangePassword(array(
            'current_password'  => Omeka_Test_Resource_Db::SUPER_PASSWORD,
            'new_password' => 'foobar6789',
            'new_password_confirm' => 'foobar6789'
        ));
        $this->_assertPasswordIs('foobar6789');
    }
    
    public function testSuperUserCanChangeOwnPasswordWithoutKnowingOriginal()
    {
        $this->user->role = 'super';
        $this->user->save();
        $this->_dispatchChangePassword(array(
            'new_password' => 'foobar6789',
            'new_password_confirm' => 'foobar6789'
        ));
        $this->_assertPasswordIs('foobar6789', 
            "Super user was not able to change the password without knowing the original.");
    }
    
    public function testChangingPasswordFailsWithInvalidPassword()
    {
        $this->_dispatchChangePassword(array(
            'current_password'  => 'wrongpassword',
            'new_password' => 'foo',
            'new_password_confirm' => 'foo'
        ));
        $this->_assertPasswordNotChanged();
    }
    
    public function testChangePasswordFailsIfPasswordNotConfirmed()
    {
        $this->_dispatchChangePassword(array(
            'current_password'  => 'foobar123',
            'new_password' => 'foo'
        ));
        $this->_assertPasswordNotChanged();
    }
    
    private function _assertPasswordNotChanged()
    {
        $this->_assertPasswordIs(Omeka_Test_Resource_Db::SUPER_PASSWORD,
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
    
    private function _dispatchChangePassword(array $form)
    {
        $this->getRequest()->setPost($form);
        $this->getRequest()->setMethod('post');
        $this->dispatch(self::FORM_URL);
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

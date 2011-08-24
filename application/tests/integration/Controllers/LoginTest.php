<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test logins.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Controller_LoginTest extends Omeka_Test_AppTestCase
{    
    public function testUpgradingHashedPasswordForUser()
    {
        $this->assertTrue($this->db instanceof Omeka_Db);
        $dbAdapter = $this->db->getAdapter();        
        // Reset the username/pass to the old style (SHA1 w/ no salt).
        $dbAdapter->update('omeka_users', 
                            array('password'  => sha1('foobar'),
                                  'salt'      => null),
                           'id = 1');
        
        // Now attempt to login, and verify that the database was upgraded, and
        // that the user account was upgraded to use a salt.
        $this->_login('foobar123', 'foobar');
        $this->assertRedirectTo('/', $this->getResponse()->getBody());
        $this->assertNotNull($dbAdapter->fetchOne("SELECT salt FROM omeka_users WHERE id = 1"));
    }
    
    public function testValidLogin()
    {
        $this->_login(Omeka_Test_Resource_Db::SUPER_USERNAME, Omeka_Test_Resource_Db::SUPER_PASSWORD);
        $this->assertRedirectTo('/');
        self::dbChanged(false);
    }
    
    public function testInvalidLogin()
    {
        $this->_login('foo', 'bar');
        $this->assertNotRedirect();
        $this->assertContains(UsersController::INVALID_LOGIN_MESSAGE, $this->getResponse()->sendResponse());
        self::dbChanged(false);
    }

    public static function roles()
    {
        return array(
            array('researcher'),
            array('contributor'),
            array('admin'),
            array('super'),
        );
    }

    /**
     * @dataProvider roles
     */
    public function testLogout($role)
    {
        $user = $this->_getDefaultUser();
        $user->role = $role;
        $this->_authenticateUser($user);
        $this->dispatch('/users/logout');
        $this->assertController('users');
        $this->assertAction('logout');
    }
    
    private function _login($username, $password)
    {
        $r = $this->getRequest();
        $r->setPost(array('username' => $username, 
                          'password' => $password))
          ->setMethod('post');
         
        $this->dispatch('users/login');
    }
}

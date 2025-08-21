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
            ['password' => sha1('foobar'), 'salt' => null],
            'id = 1'
        );

        // Now attempt to login, and verify that the database was upgraded, and
        // that the user account was upgraded to use bcrypt.
        $this->_login('foobar123', 'foobar');
        $this->assertRedirectTo('/', $this->getResponse()->getBody());
        $newUser = $dbAdapter->fetchRow("SELECT `salt`, `password` FROM omeka_users WHERE id = 1");
        $this->assertNotNull($newUser);
        $this->assertEquals($newUser['salt'], 'bcrypt');
        $this->assertTrue(password_verify('foobar', $newUser['password']));
    }

    public function testUpgradingSaltedPasswordForUser()
    {
        $this->assertTrue($this->db instanceof Omeka_Db);
        $dbAdapter = $this->db->getAdapter();
        // Reset the username/pass to the old style (SHA1 w/ salt).
        $dbAdapter->update('omeka_users',
            ['password' => sha1('0123456789abcdef' . 'barbaz'), 'salt' => '0123456789abcdef'],
            'id = 1'
        );

        // Now attempt to login, and verify that the database was upgraded, and
        // that the user account was upgraded to use bcrypt.
        $this->_login('foobar123', 'barbaz');
        $this->assertRedirectTo('/', $this->getResponse()->getBody());
        $newUser = $dbAdapter->fetchRow("SELECT `salt`, `password` FROM omeka_users WHERE id = 1");
        $this->assertNotNull($newUser);
        $this->assertEquals($newUser['salt'], 'bcrypt');
        $this->assertTrue(password_verify('barbaz', $newUser['password']));
    }

    public function testValidLogin()
    {
        $this->_login(Omeka_Test_Resource_Db::SUPER_USERNAME, Omeka_Test_Resource_Db::SUPER_PASSWORD);
        $this->assertRedirectTo('/');
    }

    public function testInvalidLogin()
    {
        $this->_login('foo', 'bar');
        $this->assertNotRedirect();
        $this->assertStringContainsString('Login information incorrect. Please try again.', $this->getResponse()->sendResponse());
    }

    public function testInvalidOldHashedPassword()
    {
        $this->assertTrue($this->db instanceof Omeka_Db);
        $dbAdapter = $this->db->getAdapter();
        // Reset the username/pass to the old style (SHA1 w/ no salt).
        $dbAdapter->update('omeka_users',
            ['password' => sha1('foobar'), 'salt' => null],
            'id = 1'
        );

        $this->_login('foobar123', 'foobar_not');
        $this->assertNotRedirect();
        $this->assertStringContainsString('Login information incorrect. Please try again.', $this->getResponse()->sendResponse());
    }

    public function testInvalidOldSaltedPassword()
    {
        $this->assertTrue($this->db instanceof Omeka_Db);
        $dbAdapter = $this->db->getAdapter();
        // Reset the username/pass to the old style (SHA1 w/ salt).
        $dbAdapter->update('omeka_users',
            ['password' => sha1('0123456789abcdef' . 'barbaz'), 'salt' => '0123456789abcdef'],
            'id = 1'
        );

        $this->_login('foobar123', 'foobar_not');
        $this->assertNotRedirect();
        $this->assertStringContainsString('Login information incorrect. Please try again.', $this->getResponse()->sendResponse());
    }

    public function testInactiveLogin()
    {
        $dbAdapter = $this->db->getAdapter();
        $dbAdapter->update('omeka_users',
            ['active' => '0'],
            'id = 1'
        );
        $this->_login(Omeka_Test_Resource_Db::SUPER_USERNAME, Omeka_Test_Resource_Db::SUPER_PASSWORD);
        $this->assertNotRedirect();
        $this->assertStringContainsString('Login information incorrect. Please try again.', $this->getResponse()->sendResponse());
    }

    public static function roles()
    {
        return [
            ['researcher'],
            ['contributor'],
            ['admin'],
            ['super'],
        ];
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
        $r->setPost(['username' => $username,
                          'password' => $password])
          ->setMethod('post');

        $this->dispatch('users/login');
    }
}

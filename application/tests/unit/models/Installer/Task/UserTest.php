<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Tests the installer task for creating a default user account.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Installer_Task_UserTest extends PHPUnit_Framework_TestCase
{
    const DB_PREFIX = 'test_';
    const USER_ID = 1;
    const ENTITY_ID = 2;
    const USERS_PLANS_ID = 3;
    
    public function setUp()
    {
        $this->dbAdapter = new Zend_Test_DbAdapter;
        $this->db = new Omeka_Db($this->dbAdapter, self::DB_PREFIX);
        $this->profiler = $this->dbAdapter->getProfiler();
    }
    
    public function testRequiresUserMetadata()
    {
        $task = new Installer_Task_User();
        try {
            $task->install($this->db);
            $this->fail("Should have thrown an exception when required metadata not given.");
        } catch (Installer_Task_Exception $e) {
            $this->assertContains("Required field", $e->getMessage());
        }
    }
    
    public function testTaskFailsIfUserNotValidates()
    {
        $task = new Installer_Task_User();
        $task->setUsername('foobar');
        $task->setPassword('foobar');
        $task->setEmail('invalid.email');
        $task->setFirstName('Foobar');
        $task->setLastName('Foobar');
        $task->setIsActive(true);
        $task->setRole('admin');
        try {
            $task->install($this->db);
            $this->fail("Should have thrown an exception for invalid user.");
        } catch (Installer_Task_Exception $e) {
            $this->assertContains("New user does not validate: Email: ", $e->getMessage());
        } catch (Omeka_Validator_Exception $e) {
            $this->fail("Wrong type of exception thrown. Should have thrown Installer_Task_Exception"
            . " instead of Omeka_Validator_Exception.");
        }
    }
    
    public function testTaskSavesNewUser()
    {
        $this->dbAdapter->appendLastInsertIdToStack(self::USERS_PLANS_ID);
        $this->dbAdapter->appendLastInsertIdToStack(self::USER_ID);
        $this->dbAdapter->appendLastInsertIdToStack(self::ENTITY_ID);
        $task = new Installer_Task_User();
        $task->setUsername('foobar');
        $task->setPassword('foobar');
        $task->setEmail('foobar@example.com');
        $task->setFirstName('Foobar');
        $task->setLastName('Foobar');
        $task->setIsActive(true);
        $task->setRole('admin');
        $task->install($this->db);
        $this->assertContains("INSERT INTO `test_users`",
            $this->profiler->getLastQueryProfile()->getQuery());
    }
}

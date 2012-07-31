<?php
require_once HELPERS;

/**
 * Tests get_users($params, $limit)
 * in helpers/UserFunctions.php
 */
class Helpers_UserFunctions_GetUsersTest extends Omeka_Test_AppTestCase
{   
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    /**
     * Tests whether the get_users helper returns data correctly from the test
     * database with no changes.
     */
    public function testCanReturnInitialData() {
        $users = get_users();
        $this->assertEquals(1, count($users));
        $this->assertEquals($users[0]->username, Omeka_Test_Resource_Db::SUPER_USERNAME);
    }
    
    /**
     * Tests filtering by user role with get_users.
     */
    public function testCanFilterByRole() {
        $users = get_users(array('role' => 'super'));
        $this->assertEquals(1, count($users));
        $this->assertEquals($users[0]->username, Omeka_Test_Resource_Db::SUPER_USERNAME);
        
        $users = get_users(array('role' => 'invalid'));
        $this->assertEquals(0, count($users));
    }
}

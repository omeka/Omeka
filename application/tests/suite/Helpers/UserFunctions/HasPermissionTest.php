<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @package Omeka
 * @subpackage Tests
 */

/**
 * Tests the is_allowed helper.
 *
 * @package Omeka
 * @subpackage Tests
 */
class Helpers_UserFunctions_HasPermissionTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $acl = get_acl();
        $acl->addResource('TestResource');
        $acl->allow(null, 'TestResource', 'allowedPrivilege');
        $acl->deny(null, 'TestResource', 'deniedPrivilege');
    }

    /**
     * Tests whether permissions checks work for anonymous users.
     *
     * No user is authenticated to start with, so no setup is required.
     */
    public function testPermissionsForAnonymous() {
        $this->assertTrue(is_allowed('TestResource', 'allowedPrivilege'));
        $this->assertFalse(is_allowed('TestResource', 'deniedPrivilege'));
        $this->assertFalse(is_allowed('TestResource', 'otherPrivilege'));
    }

    /**
     * Tests permissions checks for the superuser.
     *
     * The only difference should be that super has presumed access to
     * everything not explicitly denied, so the "other" permission
     * check should also succeed.
     */
    public function testPermissionsForSuper() {
        $this->_authenticateUser($this->_getDefaultUser());
        
        $this->assertTrue(is_allowed('TestResource', 'allowedPrivilege'));
        $this->assertFalse(is_allowed('TestResource', 'deniedPrivilege'));
        $this->assertTrue(is_allowed('TestResource', 'otherPrivilege'));
    }

    /**
     * Tests that the "resource" argument is automatically uppercased.
     */
    public function testResourceFirstLetterIsUppercased() {
        $this->assertTrue(is_allowed('testResource', 'allowedPrivilege'));
    }
}

<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @package Omeka
 * @subpackage Tests
 */
require_once HELPERS;

/**
 * Tests the has_permission helper.
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
    
    public function tearDown()
    {
        parent::tearDown();
        self::dbChanged(false);
    }

    /**
     * Tests whether permissions checks work for anonymous users.
     *
     * No user is authenticated to start with, so no setup is required.
     */
    public function testPermissionsForAnonymous() {
        $this->assertTrue(has_permission('TestResource', 'allowedPrivilege'));
        $this->assertFalse(has_permission('TestResource', 'deniedPrivilege'));
        $this->assertFalse(has_permission('TestResource', 'otherPrivilege'));
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
        
        $this->assertTrue(has_permission('TestResource', 'allowedPrivilege'));
        $this->assertFalse(has_permission('TestResource', 'deniedPrivilege'));
        $this->assertTrue(has_permission('TestResource', 'otherPrivilege'));
    }

    /**
     * Tests that the "resource" argument is automatically uppercased.
     */
    public function testResourceFirstLetterIsUppercased() {
        $this->assertTrue(has_permission('testResource', 'allowedPrivilege'));
    }
}

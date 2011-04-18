<?php

class User_AclAssertionTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        self::dbChanged(false);
        
        $this->super = new User();
        $this->super->role = 'super';
        $this->super->id = 1;
        $this->admin = new User();
        $this->admin->role = 'admin';
        $this->admin->id = 2;
    }

    public function tearDown()
    {
        release_object($this->super);
        release_object($this->admin);
        parent::tearDown();
    }

    public static function aclProvider()
    {
        return array(
            // $isAllowed, $roleUser, $resourceUser, $privilege
            array(false, 'admin', 'admin', 'change-status'),
            array(false, 'super', 'super', 'change-status'),
            array(true, 'super', 'admin', 'change-status'),
            array(false, 'admin', 'super', 'change-status'),

            array(false, 'admin', 'admin', 'change-role'),
            array(false, 'super', 'super', 'change-role'),
            array(true, 'super', 'admin', 'change-role'),
            array(false, 'admin', 'super', 'change-role'),

            array(true, 'admin', 'admin', 'change-password'),
            array(true, 'super', 'admin', 'change-password'),
            array(true, 'super', 'super', 'change-password'),
            array(false, 'admin', 'super', 'change-password'),

            array(true, 'admin', 'admin', 'edit'),
            array(true, 'super', 'admin', 'edit'),
            array(true, 'super', 'super', 'edit'),
            array(false, 'admin', 'super', 'edit'),

            array(true, 'admin', 'admin', 'show'),
            array(true, 'super', 'admin', 'show'),
            array(true, 'super', 'super', 'show'),
            array(false, 'admin', 'super', 'show'),

            array(false, 'admin', 'admin', 'delete'),
            array(false, 'super', 'super', 'delete'),
            array(true, 'super', 'admin', 'delete'),
            array(false, 'admin', 'super', 'delete'),
        );
    }

    /**
     * @dataProvider aclProvider
     */
    public function testUserAccountAcl($isAllowed, $roleUser, $resourceUser, $privilege)
    {
        $this->assertEquals($isAllowed, $this->acl->isAllowed($this->$roleUser, 
            $this->$resourceUser, $privilege));
    }
}

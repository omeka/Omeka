<?php

class Omeka_Acl_Assert_UserTest extends Omeka_Test_AppTestCase
{
    private $super;
    private $admin;

    public function setUpLegacy()
    {
        parent::setUpLegacy();

        $this->super = new User();
        $this->super->role = 'super';
        $this->super->id = 1;
        $this->admin = new User();
        $this->admin->role = 'admin';
        $this->admin->id = 2;
    }

    public function tearDownLegacy()
    {
        release_object($this->super);
        release_object($this->admin);
        parent::tearDownLegacy();
    }

    public static function aclProvider()
    {
        return [
            // $isAllowed, $roleUser, $resourceUser, $privilege
            [false, 'admin', 'admin', 'change-status'],
            [false, 'super', 'super', 'change-status'],
            [true, 'super', 'admin', 'change-status'],
            [false, 'admin', 'super', 'change-status'],

            [false, 'admin', 'admin', 'change-role'],
            [false, 'super', 'super', 'change-role'],
            [true, 'super', 'admin', 'change-role'],
            [false, 'admin', 'super', 'change-role'],

            [true, 'admin', 'admin', 'change-password'],
            [true, 'super', 'admin', 'change-password'],
            [true, 'super', 'super', 'change-password'],
            [false, 'admin', 'super', 'change-password'],

            [true, 'admin', 'admin', 'edit'],
            [true, 'super', 'admin', 'edit'],
            [true, 'super', 'super', 'edit'],
            [false, 'admin', 'super', 'edit'],

            [true, 'admin', 'admin', 'show'],
            [true, 'super', 'admin', 'show'],
            [true, 'super', 'super', 'show'],
            [false, 'admin', 'super', 'show'],

            [false, 'admin', 'admin', 'delete'],
            [false, 'super', 'super', 'delete'],
            [true, 'super', 'admin', 'delete'],
            [false, 'admin', 'super', 'delete'],
        ];
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

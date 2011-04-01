<?php

class User_AclAssertionTest extends Omeka_Test_AppTestCase
{
    public function setUp()
    {
        parent::setUp();
        self::dbChanged(false);
        
        $this->superUser = new User();
        $this->superUser->role = 'super';
        $this->superUser->id = 1;
        $this->adminUser = new User();
        $this->adminUser->role = 'admin';
        $this->adminUser->id = 2;
    }

    public static function aclProvider()
    {
        return array(
            // $isAllowed, $roleUser, $resourceUser, $privilege
            array(false, 'adminUser', 'adminUser', 'change-status'),
            array(false, 'superUser', 'superUser', 'change-status'),
            array(true, 'superUser', 'adminUser', 'change-status'),
            array(false, 'adminUser', 'superUser', 'change-status'),

            array(false, 'adminUser', 'adminUser', 'change-role'),
            array(false, 'superUser', 'superUser', 'change-role'),
            array(true, 'superUser', 'adminUser', 'change-role'),
            array(false, 'adminUser', 'superUser', 'change-role'),

            array(true, 'adminUser', 'adminUser', 'change-password'),
            array(true, 'superUser', 'adminUser', 'change-password'),
            array(true, 'superUser', 'superUser', 'change-password'),
            array(false, 'adminUser', 'superUser', 'change-password'),

            array(true, 'adminUser', 'adminUser', 'edit'),
            array(true, 'superUser', 'adminUser', 'edit'),
            array(true, 'superUser', 'superUser', 'edit'),
            array(false, 'adminUser', 'superUser', 'edit'),

            array(true, 'adminUser', 'adminUser', 'show'),
            array(true, 'superUser', 'adminUser', 'show'),
            array(true, 'superUser', 'superUser', 'show'),
            array(false, 'adminUser', 'superUser', 'show'),

            array(false, 'adminUser', 'adminUser', 'delete'),
            array(false, 'superUser', 'superUser', 'delete'),
            array(true, 'superUser', 'adminUser', 'delete'),
            array(false, 'adminUser', 'superUser', 'delete'),
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

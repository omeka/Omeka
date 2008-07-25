<?php
class UsersControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        require_once 'Omeka/Core.php';
        $core = new Omeka_Core;
        $this->bootstrap = array($core, 'initialize');
        parent::setUp();
    }
    
    public function testCallWithoutActionShouldPullFromIndexAction()
    {
        $this->dispatch('/users');
        $this->assertController('users');
        $this->assertAction('index');
    }
}
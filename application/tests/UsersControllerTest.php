<?php
class UsersControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public $core;
    
    public function setUp()
    {
        $this->bootstrap = array($this, 'testBootstrap');
        parent::setUp();
    }
    
    public function testBootstrap()
    {
        // Load mock copies of the:
        //     database
        //     config files
        //     database options
        //     acl
        //     auth
        //     plugin broker

        require_once 'Omeka/Core.php';
        $core = new Omeka_Core;
 
        // Reset the action helpers
        Zend_Controller_Action_HelperBroker::resetHelpers();
        
        // Set Theme directories and database options (this must take place
        // before the ViewRenderer is initialized).
        $core->setOptions(array('public_theme'=>'default'));
        
        if (!defined('THEME_DIR')) {
            define('THEME_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'themes');
        }
        
        $core->initializeSkeleton();
        
        // Set up the mock config file
        setup_test_config();
        
        // Set up a mock DB
        $mockDb = $this->getMock('Omeka_Db', array('getTable'), array(), '', false);
        
        $mockDbTable = $this->getMock('Omeka_Db_Table', array(), array(), '', false);

        // Mock database should return mock table objects when 'getTable' is called on it.        
        $mockDb->expects($this->any())
                ->method('getTable')
                ->will($this->returnValue($mockDbTable));
        
        $core->setDb($mockDb);
                
        $this->core = $core;
    }
    
    public function testHomePageIsIndexControllerIndexAction()
    {        
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
    }
}
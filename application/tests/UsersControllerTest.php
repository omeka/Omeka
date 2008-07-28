<?php
class UsersControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public $core;
    
    public function setUp()
    {
        $this->bootstrap = array($this, 'controllerBootstrap');
        parent::setUp();
    }
    
    public function controllerBootstrap()
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
 
        // Reset some MVC stuff, like action helpers that have been initialized.
        $this->reset();
        
        // Set Theme directories and database options (this must take place
        // before the ViewRenderer is initialized).
        $core->setOptions(array('public_theme'=>'default'));
        
        // Define this constant for the path to the public theme (need to get
        // rid of this, or just have it as a convenience for plugin writers).
        // That is because it can't be easily modified for testing purposes.
        // In fact, we can't test controller responses on the admin theme until
        // this is fixed.
        if (!defined('THEME_DIR')) {
            define('THEME_DIR', BASE_DIR . DIRECTORY_SEPARATOR . 'themes');
        }
        
        // Special skeleton initializer (so that Omeka can work without plugins
        // or a functioning database).
        $core->initializeSkeleton();
        
        // Set up the mock config file
        setup_test_config();
                
        // Let's try this without the plugin broker enabled.
        // $core->setPluginBroker($this->_getMockPluginBroker());
                
        $this->core = $core;
    }
    
    protected function _getMockPluginBroker()
    {
        $mockPluginBroker = $this->getMock('Omeka_Plugin_Broker', array(), array(), '', false);
        return $mockPluginBroker;
    }
    
    protected function _getMockDbWithMockTables()
    {
        $mockDb = $this->_getMockDb();
        
        $mockDbTable = $this->getMock('Omeka_Db_Table', array(), array(), '', false);

        // Mock database should return mock table objects when 'getTable' is called on it.        
        $mockDb->expects($this->any())
                ->method('getTable')
                ->will($this->returnValue($mockDbTable));
                
        return $mockDb;        
    }
    
    protected function _getMockDb()
    {
        return $this->getMock('Omeka_Db', array('getTable'), array(), '', false);
    }
    
    protected function _getMockTableFor($db, $class, $index=null)
    {
        $tableClass = $class . 'Table';
        
        // Get the database table object to respond to findRandomFeatured() by returning a blank item.
        $mockDbTable = $this->getMock($tableClass, array(), array(), '', false);
        
        $expectation = ($index !== null) ? $this->at($index) : $this->any();
        $db->expects($expectation)->method('getTable')->with($class)
            ->will($this->returnValue($mockDbTable));   
        
        return $mockDbTable;     
    }
    
    public function testHomePageIsIndexControllerIndexAction()
    {        
        $db = $this->_getMockDb();
        
        $table = $this->_getMockTableFor($db, 'Item', 0);
        $table = $this->_getMockTableFor($db, 'Collection', 1);
        
        $this->core->setDb($db);
                        
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
    }    
    
    public function testUsersControllerRendersAs404OnPublicTheme()
    {   
        // Set up a mock DB
        $mockDb = $this->_getMockDbWithMockTables();
        $this->core->setDb($mockDb);
                
        $this->dispatch('/users');
        $this->assertController('error');
        $this->assertAction('error');
        
        // Check the http response code is equal to 404
        $this->assertResponseCode(404);
    }
}
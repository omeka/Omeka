<?php
class UsersControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    public $core;
    
    protected $_themePhysicalPath, $_themeWebPath;
    
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
        Omeka_Context::resetInstance();

        require_once 'Omeka/Core.php';
        $core = new Omeka_Core;
                
        // Load up the admin controller plugin.
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Omeka_Controller_Plugin_Admin);
        
        // Set Theme directories and database options (this must take place
        // before the ViewRenderer is initialized).
        $core->setOptions(array('admin_theme'=>'default'));
        
        // Special skeleton initializer (so that Omeka can work without plugins
        // or a functioning database).
        $core->initializeSkeleton();

        require_once 'Item.php';

        // Initialize the paths within the view scripts. We do this here instead
        // of allowing the view object to take care of it, because the view object
        // uses database options and hard coded constants that don't translate
        // well into the testing environment. Specifically, the view object uses a
        // THEME_DIR constant that doesn't work well with the testing
        // environment, because you can't change it to use the admin theme instead
        // of the public theme midway through testing.
        $view = Zend_Registry::get('view');
        
        $themeName = 'default';
        $this->_setThemePath($view, 'admin' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $themeName);
                        
        // Set up the mock config file
        setup_test_config();
                
        // Let's try this without the plugin broker enabled.
        // $core->setPluginBroker($this->_getMockPluginBroker());
                
        $this->core = $core;
    }
    
    /**
     * @todo This can actually be abstracted to the view object itself in order
     * to eventually bypass the use of constants.
     * 
     * @param Omeka_View
     * @param string
     * @return void
     **/
    protected function _setThemePath($view, $physicalPath)
    {
        $webPath = join('/', explode(DIRECTORY_SEPARATOR, $physicalPath));
        
        $view->addScriptPath(BASE_DIR . DIRECTORY_SEPARATOR . $physicalPath);
        
        $view->addAssetPath(BASE_DIR . DIRECTORY_SEPARATOR . $physicalPath, WEB_ROOT . DIRECTORY_SEPARATOR . $webPath);
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
        
        $mockDbTable = $this->getMock($tableClass, array(), array(), '', false);
        
        $expectation = ($index !== null) ? $this->at($index) : $this->any();
        $db->expects($expectation)->method('getTable')->with($class)
            ->will($this->returnValue($mockDbTable));   
        
        return $mockDbTable;     
    }
    
    protected function _getRequestException()
    {
        return $this->getRequest()->getParam('error_handler')->exception;
    }
    
    public function testHomePageIsIndexControllerIndexAction()
    {     
        // Mock tables should be fine for this, since we're not using any complex data queries.   
        $db = $this->_getMockDbWithMockTables();        
        
        $this->core->setDb($db);
                        
        $this->dispatch('/');

        $this->assertController('index');
        $this->assertAction('index');
    }    
    
    public function testInvalidControllerRendersAs404OnAdminTheme()
    {   
        // Set up a mock DB
        $mockDb = $this->_getMockDbWithMockTables();
        $this->core->setDb($mockDb);
                
        $this->dispatch('/foobar');
        $this->assertController('error');
        $this->assertAction('error');
        
        // Check the http response code is equal to 404
        $this->assertResponseCode(404);
    }
    
    public function testItemsControllerRendersBrowsePage()
    {
        // Mock the database.
        $db = $this->_getMockDb();
        $table = $this->_getMockTableFor($db, 'Item');
        $this->core->setDb($db);
        
        // Mock the ACL, since we are using the admin/ theme.
        $acl = Omeka_Context::getInstance()->getAcl();
        $acl->loadResourceList(array('Items'=>array('browse')));
        
        // Allow access.
        $acl->allow();
                
        $this->dispatch('/items');
        $this->assertController('items');
        
        // Deny access and see where it goes.
        $acl->deny();
        
        $this->dispatch('/items');
        $this->assertController('error');
        $this->assertResponseCode(403);
    }
}
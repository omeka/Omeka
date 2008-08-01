<?php
class AdminThemeTest extends Zend_Test_PHPUnit_ControllerTestCase
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
        require_once 'Omeka/Context.php';
        Omeka_Context::resetInstance();
        
        // This is a subclass of Omeka_Core, which has had its routeStartup()
        // hook redefined to load a custom sequence that is easier to test with.
        // This may point towards the need for further refactorings.
        require_once 'CoreTestPlugin.php';
        $core = new CoreTestPlugin;
                
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin($core);
        
        // Initialize a Mock Auth object. Mocks can only be initialized within
        // the testing class, so this would be here and not inside the
        // CoreTestPlugin class.  This auth object will always signal that a user has been authenticated.
        require_once 'Zend/Auth.php';
        $auth = $this->getMock('Zend_Auth', array('hasIdentity'), array(), '', false);
        $auth->expects($this->any())->method('hasIdentity')->will($this->returnValue(true));
        $core->setAuth($auth);        
                
        $this->core = $core;
        
        // Mock the ACL to always give access.
        $this->_setMockAcl(true);
        
    }
    
    protected function _setLoggedInUser($userRole = 'super')
    {        
        //logged-in user dependency
        $user = new User;
        $user->id = 1;
        $user->username = "foobar";
        $user->first_name = "Foo";
        $user->last_name = "Bar";
        $user->role = $userRole;

        $this->core->setCurrentUser($user);
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
        require_once 'Omeka/Db.php';
        require_once 'Omeka/Db/Table.php';
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
    
    protected function _setMockDbForItem()
    {
        // Mock the database.
        $db = $this->_getMockDb();
        $table = $this->_getMockTableFor($db, 'Item');
        $this->core->setDb($db);
    }
    
    /**
     * Easiest usage to dump exceptions thrown during the tests:
     * var_dump($this->_getRequestException()->getMessage());exit;
     * 
     * @return Exception
     **/
    protected function _getRequestException()
    {
        return $this->getRequest()->getParam('error_handler')->exception;
    }

    protected function _setMockAcl($allowAccess=true)
    {
        // Mock the ACL.
         $acl = $this->getMock('Omeka_Acl', array('isAllowed', 'get', 'checkUserPermission'), array(), '', false);

         $acl->expects($this->any())->method('isAllowed')->will($this->returnValue($allowAccess));
         $acl->expects($this->any())->method('checkUserPermission')->will($this->returnValue($allowAccess));
         $this->core->setAcl($acl);        
    }
    
    public function assertAdmin404Page()
    {
        $this->assertController('error');
        $this->assertAction('not-found');
        // Check the http response code is equal to 404
        $this->assertResponseCode(404);
                        
        $this->assertQuery('div.filenotfound', "Could not find a div with class = filenotfound.  This indicates that the admin theme 404 page is not loading properly.");        
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
        
        // Zend_Controller_Front::getInstance()->getResponse()->headersSentThrowsException = false;
        $this->dispatch('/foobar');
        $this->assertAdmin404Page();
    }

    public function testItemsControllerRendersBrowsePage()
    {
        // Mock the database.
        $this->_setMockDbForItem();
                
        $this->dispatch('/items');
        
        $this->assertController('items');
        $this->assertAction('browse');
        $this->assertNotRedirect();
        $this->assertResponseCode(200);
    }
    
    public function testAccessDeniedErrorsGoTo403Page()
    {
        $this->_setMockDbForItem();
        
        // Deny access and see where it goes.
        $this->_setMockAcl(false);
            
        // User should still be logged in.
        $this->_setLoggedInUser('super');
        
        // Assert that the current user is a super user.
        $this->assertEquals('super', $this->core->getCurrentUser()->role);    
        
        // Assert that this user does not have access.
        $this->assertFalse($this->core->getAcl()->isAllowed('super', 'Items', 'browse'));
        
        // Assert that we aren't throwing exceptions for this request.
        $this->assertFalse($this->frontController->throwExceptions());
                
        $this->dispatch('/items');
        // var_dump($this->getResponse());exit;
        // var_dump($this->getRequest()->getActionName());exit;
        
        $this->assertController('error');
        $this->assertAction('forbidden');
        $this->assertResponseCode(403);        
    }
    
    public function testCanRouteToItemShowPage()
    {
        // Fake a database connection and wrap it in a legit Omeka_Db instance (with no prefix).
        $mockDbh = $this->getMock('Zend_Db_Adapter_Mysqli', array(), array(), '', false);
        $realDb = new Omeka_Db($mockDbh);
        $this->core->setDb($realDb);
        
        // All queries to this fake db connection should return fake statement objects (so we don't worry about the results).
        $mockStmt = $this->getMock('Zend_Db_Statement_Mysqli', array(), array(), '', false);
        $mockDbh->expects($this->any())->method('query')->will($this->returnValue($mockStmt));
        
        // For the sake of testing, any rows that are fetched from the DB should appear to be a row from the items table.
        $mockDbh->expects($this->atLeastOnce())->method('fetchRow')->will($this->returnValue(array('id'=>1, 'public'=>0, 'featured'=>0)));
                
        $this->dispatch('/items/show/1');
        
        $this->assertController('items');
        $this->assertAction('show');
        // Assert that the <body> tag has a class="items".  This is how we verify that the HTML has actually loaded.
        $this->assertQuery('body.items');
        // This should most definitely not be a redirect to the login page.
        $this->assertNotRedirect();
        $this->assertResponseCode(200);
    }
    
    public function testAccessingAnInvalidItemRedirectsToNotFoundWithAHelpfulMessage()
    {
        // Make a mock database object that uses all mock tables. That way, no
        // items will appear to exist in the database.
        $mockDb = $this->_getMockDbWithMockTables();
        $this->core->setDb($mockDb);
        
        $mockTable = $mockDb->getTable('Item');
        
        // All finds should return null.
        $mockTable->expects($this->any())->method('find')->will($this->returnValue(null));
        
        // The item does not exist in the table, as far as we're concerned.
        $mockTable->expects($this->any())->method('checkExists')->will($this->returnValue(false));
        
        $this->dispatch('/items/show/1');        
        $this->assertAdmin404Page();
    }
    
    public function testLoginPageWillDisplayWhenAttemptingToAccessAdminInterface()
    {
        // Make an auth object that refuses to believe that anyone is authenticated.
        $mockAuth = $this->getMock('Zend_Auth', array('hasIdentity'), array(), '', false);
        $mockAuth->expects($this->any())->method('hasIdentity')->will($this->returnValue(false));
        $this->core->setAuth($mockAuth);
        
        // Make a mock db object.
        $mockDb = $this->_getMockDbWithMockTables();
        $this->core->setDb($mockDb);
        
        // Verify that a user has not been authenticated.
        $this->assertFalse(Omeka_Context::getInstance()->getAuth()->hasIdentity());
        
        $this->dispatch('/items');
        // var_dump($this->_getRequestException()->getMessage());exit;
        $this->assertController('users');
        $this->assertAction('login');
    }
}
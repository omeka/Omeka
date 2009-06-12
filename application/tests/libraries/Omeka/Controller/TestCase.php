<?php 

/**
 * Has some helper methods for mocking ACL, DB, etc. so that controller calls
 * work properly.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
abstract class Omeka_Controller_TestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    public $core;
    
    public function setUp()
    {
        $bootstrapper = new Omeka_Test_Bootstrapper($this);
        $this->bootstrap = array($bootstrapper, 'bootstrap');
        parent::setUp();
    }
        
    public function tearDown()
    {
        Omeka_Context::resetInstance();
        parent::tearDown();
    }
    
    /**
     * Implement in test cases to set up bootstrap resources.
     * 
     * @param Omeka_Core $bootstrap
     * @return void
     **/
    abstract public function setUpBootstrap($bootstrap);
    
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
    
    /**
     * Example usage when configuring bootstrap:
     * <code>
     * $mockDbResource = $this->_getMockBootstrapResource('Db', $this->_getMockDbWithMockTables());
     * $this->core->registerPluginResource($mockDbResource);
     * </code>
     * 
     * @param string
     * @param mixed
     * @return mixed
     **/
    protected function _getMockBootstrapResource($resourceName, $returnVal)
    {
        // Create a mock resource object for each of the desired whatevers.
       $mockClassName = 'TestMock_' . $resourceName;
       $methods = array('init');
       $className = 'Zend_Application_Resource_ResourceAbstract';
       $callOriginalConstructor = false;
       $callOriginalClone = false;
       $callAutoload = true;
       if (!class_exists($mockClassName)) {
           $mockDefinition = PHPUnit_Framework_MockObject_Mock::generate(
                $className,
                $methods,
                $mockClassName,
                $callOriginalConstructor,
                $callOriginalClone,
                $callAutoload);
       }
       
       
       // Instantiate the mock resource and tell it to always return the value
       // we have specified via the 'return' key of the original array.
       $mockResourceObj = new $mockClassName;   
       $mockResourceObj->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
                   ->method('init')
                   ->will(new PHPUnit_Framework_MockObject_Stub_Return($returnVal));
       // Make sure it also thinks it has the correct resource name.
       $mockResourceObj->_explicitType = $resourceName;
       
       return $mockResourceObj;    
    }
    
    /**
     * Convenience method for configuring default access to the public theme.
     * 
     * @param Omeka_Core
     * @return void
     **/
    protected function _configPublicThemeBootstrap($bootstrap)
    {
        $mockDbResource = $this->_getMockBootstrapResource('Db', $this->_getMockDbWithMockTables());
        $bootstrap->registerPluginResource($mockDbResource);
        $bootstrap->setOptions(array(
            'resources'=> array(
                'Config' => array(),
                'FrontController' => array(),
                'Acl' => array(),
                'Options' => array('options'=> array('public_theme'=>'default')),
                'Theme' => array('basePath'=>BASE_DIR . '/themes', 'webBasePath'=>WEB_ROOT . '/themes')
            )
        ));
    }
}

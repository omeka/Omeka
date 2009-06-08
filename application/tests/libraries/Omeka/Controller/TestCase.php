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
                
        $this->core = $core;
        
        $this->init();
    }
    
    public function init()
    {}
    
    
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
}

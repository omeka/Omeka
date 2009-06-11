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
        $core = new Omeka_Core(null);
        $core->setOptions(array(
            'pluginpaths'=>
                array(
                    'Omeka_Core_Resource' => LIB_DIR . '/Omeka/Core/Resource/',
                    'Omeka_Test_Resource' => TEST_LIB_DIR . '/Omeka/Test/Resource/'),
            'resources'=>array(
                'Acl' => array(),
                'Config' => array(), 
                'Options' => array(), 
                'FrontController' => array(),
                'Db' => array())));
        
        $core->bootstrap();
                
        $this->core = $core;
        
        $this->init();
    }
    
    public function tearDown()
    {
        Omeka_Context::resetInstance();
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

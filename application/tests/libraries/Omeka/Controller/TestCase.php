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
        
        $mockPluginBroker->expects($this->any())
                         ->method('__call')
                         ->will($this->returnCallback(array($this, 'mockApplyFiltersForMockPluginBroker')));
                    
        return $mockPluginBroker;
    }
    
    public function mockApplyFiltersForMockPluginBroker($hook, $args)
    {
        // return the array to be filtered by apply filters
        if ($hook == 'applyFilters') {
            //$filterName = $args[0];
            $filterArray = $args[1];
            return $filterArray;
        }
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
     * Convenience method for configuring default access to the public theme.
     * 
     * @param Omeka_Core
     * @return void
     **/
    protected function _configPublicThemeBootstrap($bootstrap)
    {
        $mockDbResource = Omeka_Test_Bootstrapper::getMockBootstrapResource('Db', $this->_getMockDbWithMockTables());
        $bootstrap->registerPluginResource($mockDbResource);
        
        $mockPluginBrokerResource = Omeka_Test_Bootstrapper::getMockBootstrapResource('PluginBroker', $this->_getMockPluginBroker());
        $bootstrap->registerPluginResource($mockPluginBrokerResource);
         
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
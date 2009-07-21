<?php
/**
 * Encapsulates loading and configuring access to the test database.
 *
 * @package Omeka_Testing
 * @copyright Center for History and New Media, 2009
 **/
abstract class Omeka_Model_TestCase extends PHPUnit_Framework_TestCase
{   
    public function setUp()
    {
        $bootstrapper = new Omeka_Test_Bootstrapper($this);
        $bootstrapper->bootstrap();
    }
             
    public function getAdapter()
    {
        return $this->core->getResource('Db');
    }
    
    public function setUpBootstrap($bootstrap)
    {
        $bootstrap->registerPluginResource('Db');
        
        $mockPluginBrokerResource = Omeka_Test_Bootstrapper::getMockBootstrapResource('PluginBroker', $this->_getMockPluginBroker());
        $bootstrap->registerPluginResource($mockPluginBrokerResource);
    }
    
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
    
    /**
     * Asserts that the given table is empty.
     *
     * @param $tableName string Table name
     */
    protected function _assertTableIsEmpty($tableName)
    {
        // Verify that there are no items in the test table.
        $sql = "SELECT COUNT(*) FROM $tableName";
        $count = $this->getAdapter()->fetchOne($sql);
        $this->assertEquals(0, $count, "$tableName was not empty as expected.");
    }
    
    public function tearDown()
    {
        Omeka_Context::resetInstance();
        parent::tearDown();
    }
} 
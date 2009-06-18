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
} 
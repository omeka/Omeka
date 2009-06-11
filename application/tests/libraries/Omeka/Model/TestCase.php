<?php
/**
 * Encapsulates loading and configuring access to the test database.
 *
 * @package Omeka_Testing
 * @copyright Center for History and New Media, 2009
 **/
abstract class Omeka_Model_TestCase extends PHPUnit_Framework_TestCase
{
    protected $_adapter;
    
    public function setUp()
    {
        $dbResource = new Omeka_Test_Resource_Db;
        $this->_adapter = $dbResource->init();
        Omeka_Context::getInstance()->setDb($this->_adapter);
    }
    
    public function tearDown()
    {
        Omeka_Context::resetInstance();
    }
} 
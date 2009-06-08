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
        $dbResource = new Omeka_Core_Resource_DbTest;
        $this->_adapter = $dbResource->init();
    }
} 
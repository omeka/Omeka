<?php

class Omeka_Storage_StorageTest extends PHPUnit_Framework_TestCase
{
    public function testNoOptions()
    {
        new Omeka_Storage();
    }
    
    /**
     * Test passing an adapter class name that's not a defined class,
     * should throw Omeka_Storage_Exception.
     * 
     * @expectedException Omeka_Storage_Exception
     */
    public function testNonExistingAdapterName()
    {
        new Omeka_Storage(array('adapter' => 'NotAClass'));
    }

    /**
     * Test passing an adapter class name that's not an interface
     * implementer, should throw Omeka_Storage_Exception.
     * 
     * @expectedException Omeka_Storage_Exception
     */
    public function testBadAdapterName()
    {
        new Omeka_Storage(array('adapter' => 'stdClass'));
    }

    /**
     * Test passing an adapter class name that's not an interface
     * implementer, should throw Omeka_Storage_Exception.
     * 
     * @expectedException Omeka_Storage_Exception
     */
    public function testNullAdapter()
    {
        new Omeka_Storage(array('adapter' => null));
    }

    /**
     * Test passing an adapter class name that's not an interface
     * implementer, should throw Omeka_Storage_Exception.
     * 
     * @expectedException Omeka_Storage_Exception
     */
    public function testBadAdapter()
    {
        $class = new stdClass;
        new Omeka_Storage(array('adapter' => $class));
    }
}

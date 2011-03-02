<?php

class Omeka_StorageTest extends PHPUnit_Framework_TestCase
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

    /**
     * Test using the magic calling for adapter methods with no adapter.
     *
     * @expectedException Omeka_Storage_Exception
     */
    public function testStoreWithNoAdapter()
    {
        $storage = new Omeka_Storage;
        $storage->store('path', 'destination');
    }

    public function testTempDirDefault()
    {
        $storage = new Omeka_Storage;
        $this->assertEquals(sys_get_temp_dir(), $storage->getTempDir());
    }

    public function testTempDirCustom()
    {
        $storage = new Omeka_Storage;
        $customDir = '/';
        $storage->setTempDir($customDir);
        $this->assertEquals($customDir, $storage->getTempDir());
    }
}

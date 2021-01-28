<?php

class Omeka_StorageTest extends Omeka_Test_TestCase
{
    public function testNoOptions()
    {
        $storage = new Omeka_Storage();
        $this->assertNull($storage->getAdapter());
    }

    /**
     * Test passing an adapter class name that's not a defined class,
     * should throw Omeka_Storage_Exception.
     */
    public function testNonExistingAdapterName()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        new Omeka_Storage(array('adapter' => 'NotAClass'));
    }

    /**
     * Test passing an adapter class name that's not an interface
     * implementer, should throw Omeka_Storage_Exception.
     */
    public function testBadAdapterName()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        new Omeka_Storage(array('adapter' => 'stdClass'));
    }

    /**
     * Test passing an adapter class name that's not an interface
     * implementer, should throw Omeka_Storage_Exception.
     */
    public function testNullAdapter()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        new Omeka_Storage(array('adapter' => null));
    }

    /**
     * Test passing an adapter class name that's not an interface
     * implementer, should throw Omeka_Storage_Exception.
     */
    public function testBadAdapter()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
        $class = new stdClass;
        new Omeka_Storage(array('adapter' => $class));
    }

    /**
     * Test using the magic calling for adapter methods with no adapter.
     */
    public function testStoreWithNoAdapter()
    {
        $this->setExpectedException('Omeka_Storage_Exception');
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

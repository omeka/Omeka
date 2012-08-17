<?php

class Omeka_Storage_Adapter_FilesystemTest extends PHPUnit_Framework_TestCase
{
    private $_options = array(
        'localDir' => '/foo/bar',
        'webDir' => '/foobar',
    );

    public static $tempDir;

    public static function setUpBeforeClass()
    {
        $tempDirResource = new Omeka_Test_Resource_Tempdir;
        self::$tempDir = $tempDirResource->init();
    }

    public function testDefaultLocalDir()
    {
        $storage = new Omeka_Storage_Adapter_Filesystem();
        $options = $storage->getOptions();
        if (defined('FILES_DIR')) {
            $this->assertEquals($options['localDir'], FILES_DIR);
        } else {
            $this->assertEquals($options['localDir'], null);
        }
    }

    public function testDefaultWebDir()
    {
        $storage = new Omeka_Storage_Adapter_Filesystem();
        $options = $storage->getOptions();
        if (defined('WEB_FILES')) {
            $this->assertEquals($options['webDir'], WEB_FILES);
        } else {
            $this->assertEquals($options['webDir'], null);
        }
    }

    public function testAllOptions()
    {
        $storage = new Omeka_Storage_Adapter_Filesystem($this->_options);
        $this->assertEquals($this->_options, $storage->getOptions());
    }

    /**
     * @expectedException Omeka_Storage_Exception
     */
    public function testInvalidOption()
    {
        $storage = new Omeka_Storage_Adapter_Filesystem(array('foobar' => true));
    }
    
    public function testCanStore()
    {
        $cantStore = new Omeka_Storage_Adapter_Filesystem($this->_options);
        $this->assertFalse($cantStore->canStore());
        $tempDir = self::$tempDir;
        $canStore = new Omeka_Storage_Adapter_Filesystem(array(
            'localDir' => $tempDir,
        ));
        $canStore->setUp();
        $this->assertTrue($canStore->canStore());
    }

    public static function localDirs()
    {
        return array(
            array(null, false),
            array('/foo/bar' . mt_rand(), true),
        );
    }

    /**
     * @dataProvider localDirs
     */
    public function testMove($localDir, $throwsException)
    {
        if (!$localDir) {
            $localDir = self::$tempDir;
        }
        
        $storage = new Omeka_Storage_Adapter_Filesystem(array(
            'localDir' => $localDir,
        ));
        $testFile = tempnam(self::$tempDir, 'omeka_storage_filesystem_test');
        try {
            $storage->move(basename($testFile), 'foo.txt');
            $this->assertTrue(file_exists("$localDir/foo.txt"));
            if ($throwsException) {
                $this->fail();
            }
        } catch (Omeka_Storage_Exception $e) {
             if (!$throwsException) {
                $this->fail($e->getMessage());
             }
        }
    }

    /**
     * @dataProvider localDirs
     */
    public function testStore($localDir, $throwsException)
    {
        if (!$localDir) {
            $localDir = self::$tempDir;
        }
        
        $storage = new Omeka_Storage_Adapter_Filesystem(array(
            'localDir' => $localDir,
        ));
        $testFile = tempnam(self::$tempDir, 'omeka_storage_filesystem_test');
        try {
            $storage->store($testFile, 'foo.txt');
            $this->assertTrue(file_exists("$localDir/foo.txt"));
            if ($throwsException) {
                $this->fail();
            }
        } catch (Omeka_Storage_Exception $e) {
             if (!$throwsException) {
                $this->fail($e->getMessage());
             }
        }
    }

    public function testDelete()
    {
        $tempDir = self::$tempDir;
        $storage = new Omeka_Storage_Adapter_Filesystem(array(
            'localDir' => $tempDir,
        ));
        $testFile = tempnam($tempDir, 'omeka_storage_filesystem_test');

        $storage->delete(basename($testFile));
        $this->assertFalse(file_exists($testFile));

        try {
            $storage->delete(basename($testFile));
        } catch (Omeka_Storage_Exception $e) {
            $this->fail('An exception was thrown for trying to delete a missing file');
        }
    }

    public static function notWritable()
    {
        return array(
            array('store', array(self::_getRandomFilename(), 
                                 self::_getRandomFilename())),
            array('move', array(self::_getRandomFilename(),
                                self::_getRandomFilename())),
        );
    }

    /**
     * @dataProvider notWritable
     * @expectedException Omeka_Storage_Exception
     */
    public function testNotWritable($method, $args)
    {
        // Random directory should not exist, therefore not writable.
        $storage = new Omeka_Storage_Adapter_Filesystem(array(
            'localDir' => '/foo/bar' . mt_rand(),
        ));
        call_user_func_array(array($storage, $method), $args);
        $this->fail();
    }

    private static function _getRandomFilename()
    {
        return 'foo.txt' . mt_rand();
    }
}

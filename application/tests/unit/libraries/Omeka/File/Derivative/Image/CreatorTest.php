<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_File_Derivative_Image_Creator_CreatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->convertDir = '/opt/local/bin';
        $this->invalidFile = '/foo/bar/baz.html';
        $this->validFilePath = dirname(__FILE__) . '/_files/valid-image.jpg';
        $this->validMimeType = 'image/jpeg';
        $this->fullsizeImgPath = dirname(__FILE__) . '/_files/fullsize';
        if (!is_dir($this->fullsizeImgPath)) {
            mkdir($this->fullsizeImgPath);
        }
        $this->derivativeFilename = 'valid-image_deriv.jpg';
        // If we set up a test log, then log the ImageMagick commands instead
        // of executing via the commandline.
        $this->logWriter = new Zend_Log_Writer_Mock;
        $this->testLog = new Zend_Log($this->logWriter);
    }
    
    public function testConstructor()
    {
        $creator = new Omeka_File_Derivative_Image_Creator($this->convertDir);
        $this->assertEquals("{$this->convertDir}/convert", $creator->getConvertPath());
    }
    
    public function testCreateWithoutProvidingDerivativeFilename()
    {
        try {
            $creator = new Omeka_File_Derivative_Image_Creator($this->convertDir);
            $creator->create($this->validFilePath, '', $this->validMimeType);
        } catch (InvalidArgumentException $e) {
            $this->assertContains("Invalid derivative filename", $e->getMessage());
            return;
        }
        $this->fail("create() should have failed when a derivative filename was not provided.");
    }
    
    public function testCreateWithInvalidConvertPath()
    {
        try {
            $creator = new Omeka_File_Derivative_Image_Creator('/foo/bar');
        } catch (Omeka_File_Derivative_Exception $e) {
            $this->assertContains("invalid directory", $e->getMessage());
            return;
        }
        $this->fail("Instantiating with a valid convert path failed to throw an exception.");
    }
    
    public function testCreate()
    {
        $creator = new Omeka_File_Derivative_Image_Creator($this->convertDir);
        // Should do nothing.
        $creator->create($this->validFilePath, $this->derivativeFilename, $this->validMimeType);
    }
    
    public function testCreateWithInvalidOriginalFile()
    {
        $creator = new Omeka_File_Derivative_Image_Creator($this->convertDir);
        try {
            $creator->create($this->invalidFile, $this->derivativeFilename, $this->validMimeType);
        } catch (Exception $e) {
            $this->assertContains("does not exist", $e->getMessage());
            return;
        }
        $this->fail("Failed to throw an exception when given an invalid original file.");
    }
    
    public function testAddDerivativeWithInvalidDestPath()
    {
        $creator = new Omeka_File_Derivative_Image_Creator($this->convertDir);
        try {
            $creator->addDerivative("/foo/bar/baz", 20);
        } catch (Exception $e) {
            $this->assertContains("directory does not exist", $e->getMessage());
            return;
        }
        $this->fail("Failed to throw exception when given invalid storage directory for image derivatives.");
    }

    public function testCreateWithDerivativeImgSize()
    {
        $creator = new Omeka_File_Derivative_Image_Creator($this->convertDir);
        $creator->addDerivative($this->fullsizeImgPath, 10);
        $creator->create($this->validFilePath, $this->derivativeFilename, $this->validMimeType);
        $newFilePath = $this->fullsizeImgPath . '/' . $this->derivativeFilename;
        $this->assertTrue(file_exists($newFilePath));
        unlink($newFilePath);
    }
        
    public function testCreateWithDerivativeCommandArgs()
    {
        $creator = new Omeka_File_Derivative_Image_Creator($this->convertDir);
    }   
}

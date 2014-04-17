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
class Omeka_File_Derivative_CreatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $config = new Omeka_Test_Resource_Config;
        $configIni = $config->init();
        if (isset($configIni->paths->imagemagick)) {
            $this->convertDir = $configIni->paths->imagemagick;
        } else {
            $this->convertDir = dirname(`which convert`);
        }
        
        $this->invalidFile = '/foo/bar/baz.html';
        $this->validFilePath = dirname(__FILE__) . '/_files/valid-image.jpg';
        $this->validMimeType = 'image/jpeg';
        $this->fullsizeImgType = 'fullsize';
        $this->derivativeFilename = 'valid-image_deriv.jpg';
        // If we set up a test log, then log the ImageMagick commands instead
        // of executing via the commandline.
        $this->logWriter = new Zend_Log_Writer_Mock;
        $this->testLog = new Zend_Log($this->logWriter);

        $this->creator = new Omeka_File_Derivative_Creator;
        $this->strategy = new Omeka_File_Derivative_Strategy_ExternalImageMagick;
        $this->creator->setStrategy($this->strategy);
    }
    
    public function testCreateWithoutProvidingDerivativeFilename()
    {
        try {
            $this->creator->create($this->validFilePath, '', $this->validMimeType);
        } catch (InvalidArgumentException $e) {
            $this->assertContains("Invalid derivative filename", $e->getMessage());
            return;
        }
        $this->fail("create() should have failed when a derivative filename was not provided.");
    }
    
    public function testCreate()
    {
        $this->creator->create($this->validFilePath, $this->derivativeFilename, $this->validMimeType);
    }
    
    public function testCreateWithInvalidOriginalFile()
    {
        try {
            $this->creator->create($this->invalidFile, $this->derivativeFilename, $this->validMimeType);
        } catch (Exception $e) {
            $this->assertContains("is not readable", $e->getMessage());
            return;
        }
        $this->fail("Failed to throw an exception when given an invalid original file.");
    }
    
    public function testAddDerivativeWithInvalidDerivativeType()
    {
        try {
            $this->creator->addDerivative("/foo/bar/baz", 20);
        } catch (Exception $e) {
            $this->assertContains("Invalid derivative type", $e->getMessage());
            return;
        }
        $this->fail("Failed to throw exception when given invalid type name for image derivatives.");
    }

    public function testCreateWithDerivativeImgSize()
    {
        $this->strategy->setOptions(array('path_to_convert' => $this->convertDir));
        $this->creator->addDerivative($this->fullsizeImgType, 10);
        $this->creator->create($this->validFilePath, $this->derivativeFilename, $this->validMimeType);
        $newFilePath = dirname($this->validFilePath) . '/' 
            . $this->fullsizeImgType . '_' . $this->derivativeFilename;
        $this->assertTrue(file_exists($newFilePath));
        unlink($newFilePath);
    }

    public function testCreateWithInvalidConvertPath()
    {
        $this->strategy->setOptions(array('path_to_convert' => '/foo/bar'));
        $this->creator->addDerivative($this->fullsizeImgType, 10);
        try {
            $this->creator->create($this->validFilePath, $this->derivativeFilename, $this->validMimeType);
        } catch (Omeka_File_Derivative_Exception $e) {
            $this->assertContains("invalid directory", $e->getMessage());
            return;
        }
        $this->fail("Instantiating with a valid convert path failed to throw an exception.");
    }
}

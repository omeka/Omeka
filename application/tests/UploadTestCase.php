<?php 
/**
* 
*/
class UploadTestCase extends OmekaTestCase
{
	protected $pathToTestFile;
	
	protected $derivativeFilename;
	
	const NO_IMAGEMAGICK = 'ImageMagick is not properly configured.  Please check your settings and then try again.';
	const INVALID_CONSTRAINTS = "The sizes for derivative images have not been configured properly.";
	
	public function setUp()
	{
		parent::setUp();
		
		$this->pathToTestFile = APP_DIR . '/tests/assets/test_image.jpg';
		
		if(!is_writeable(dirname($this->pathToTestFile)) or !is_readable($this->pathToTestFile)) {
			echo $this->pathToTestFile . ' must have read/write permissions to run UploadTestCase';exit;
		}
		
		Zend_Registry::set('options', array());
		
		//Set up the options for the upload
		set_option('thumbnail_constraint', 100);
		set_option('fullsize_constraint', 500);
		set_option('square_thumbnail_constraint', 100);
		
		//Set up the $_FILES array with the file
		$_FILES['item']['name'][0] = 'test_image.jpg';
		$_FILES['item']['error'][0] = 0;
		$_FILES['item']['tmp_name'][0] = $this->pathToTestFile;
	}
		
	protected function setUpImageMagick()
	{
		$config = Zend_Registry::get('config_ini');
		set_option('path_to_convert', $config->paths->imagemagick);		
	}
	
	protected function expectUploadException($text)
	{
		try {
			File::handleUploadErrors('item');
			
			//Fail if it doesn't throw an exception
			$this->fail();
		} catch (Omeka_Upload_Exception $e) {
			$this->assertEqual($e->getMessage(), $text);
		}		
	}
	
	public function testUnconfiguredImageMagickThrowsException()
	{					
		$this->expectUploadException(self::NO_IMAGEMAGICK);
		
		//Unset the thumbnail constraint and see what kind of error results
		set_option('thumbnail_constraint', null);
		
		//Set the path to ImageMagick so we don't get the same error as last time
		$this->setUpImageMagick();
		
		$this->expectUploadException(self::INVALID_CONSTRAINTS);
	}
	
	//Set the upload error to indicate that no file was uploaded, then test to see if the FILES array is still empty
	public function testNotUploadingAFileCleansFilesArray()
	{
		$_FILES['item']['error'][0] = UPLOAD_ERR_NO_FILE;
		
		$this->setUpImageMagick();
		
		File::handleUploadErrors('item');
		
		//There was a single file uploaded, so assert that now there are no files uploaded
		$this->assertEqual(count($_FILES['item']['error']), 0);
	}
}
 
?>

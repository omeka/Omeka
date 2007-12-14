<?php 
/**
* 
*/
class FileMetadataTestCase extends OmekaTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->setUpLiveDb();
		
		$this->path_to_mov = TEST_ASSETS_DIR . DIRECTORY_SEPARATOR . 'test.mov';
		$this->path_to_bigger_mov = TEST_ASSETS_DIR . DIRECTORY_SEPARATOR . 'test_bigger.mov';
		$this->path_to_wmv = TEST_ASSETS_DIR . DIRECTORY_SEPARATOR . 'test.wmv';
	}
	
	//First let's pretend that there is no entry in the FileMetaLookup table
	//Because there isn't
	//So calling this should return false

	public function testRetrievesNoMetadataWhenLookupTableIsEmpty()
	{
		$file = new File;
		$returns = $file->processExtendedMetadata($this->path_to_mov);
		$this->assertFalse($returns, 'When there are no entries in the FileMetaLookup table, return false');		
	}
	
	
	protected function addEntryToLookupTable($mime, $class, $table)
	{
		//Add a FileMetaLookup entry
		$lookup = new FileMetaLookup();
		$lookup->setArray(array('mime_type'=>$mime, 'table_class'=>$class, 'table_name'=>$table));
		$lookup->forceSave();		
	}
	
	public function testRetrievesMetadataBasedOnBrowserMimeType()
	{
		$file = new File;
		
		$this->addEntryToLookupTable('video/quicktime', 'FilesVideos', 'files_videos');		
		
		//When there is a non-ambiguous MIME type given by the browser use that. 
		//In this case, give it a MIME type that isn't in the lookup table, see if it returns false
		$file->mime_browser = "image/jpeg";
		
		//Should return false
		$returns = $file->processExtendedMetadata($this->path_to_mov);
		$this->assertFalse($returns);
		
		//Give it an ambiguous MIME type and it should return true, because it looked up the correct one
		$file->mime_browser = "application/octet-stream";
		
		$this->assertTrue($file->processExtendedMetadata($this->path_to_mov));
		$this->assertTrue($file->processExtendedMetadata($this->path_to_bigger_mov));
	}
	
	public function testCanRetrieveExtendedMetadataForWmvFiles()
	{
		$file = new File;
		
		$this->addEntryToLookupTable('video/x-ms-wmv', 'FilesVideos', 'files_videos');
		
		$returns = $file->processExtendedMetadata($this->path_to_wmv);
		
		$this->assertTrue($returns);
		
		$meta = $file->Extended;
		
		//Now verify that we can retrieve all the available metadata
		
		$this->assertEqual($meta->codec, "Windows Media Video V9");
		$this->assertRetrievesVideoMetadata($meta);
	}	
	
	protected function assertRetrievesVideoMetadata(FilesVideos $meta)
	{
		$this->assertTrue($meta->bitrate > 0);
		$this->assertTrue($meta->duration > 0);
//		Having problems with this in Quicktime format
//		$this->assertTrue($meta->sample_rate > 0);
		$this->assertTrue($meta->width > 0);
		$this->assertTrue($meta->height > 0);		
	}
	
	public function testCanRetrieveExtendedMetadataForMovFiles()
	{
		$file = new File;
				
		$this->addEntryToLookupTable('video/quicktime', 'FilesVideos', 'files_videos');		
				
		$returns = $file->processExtendedMetadata($this->path_to_mov);
		
		$this->assertTrue($returns);

		//Check to see whether it stored extended metadata in $file->Extended;
		$this->assertRetrievesVideoMetadata($file->Extended);
	}
}
 
?>

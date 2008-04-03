<?php 
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'File.php';	
class FileTestCase extends OmekaTestCase
{
	function testSanitizeFilename()
	{
		$file = new File;
		
		$typical = "foo.jpg";
		
		$filenames = array(
			'Typical'	=>
					array('foo.jpg', 'foo.jpg'),
			'Trailing Dots' => 
					array('foo..', 'foo'),
			'Uppercase' =>
					array('FOO', 'foo'),
			'Invalid characters' =>
					array('"*/:<foo.jpg>?\\|\'&;#', 'foo.jpg'),
			'Multiple Dots' =>
					array('foo.copy.jpg', 'foocopy.jpg'),
			'International' =>
					array('セックシ女の人.jpg', 'セックシ女の人.jpg'),
			'Intl Multiple Dots' =>
					array('セック.シ女の人.jpg', 'セックシ女の人.jpg'),
			'Non-printable Characters' =>
					array("foo\n\t.jpg",'foo.jpg'),
			'Spaces To Hyphens' =>
					array('foo bar.jpg', 'foo-bar.jpg')
		);
		
		foreach ($filenames as $msg => $tests) {
			list($test, $desiredOutput) = $tests;
			
			$sanitized = $file->sanitizeFilename($test);
			
			$this->assertEqual($sanitized,$desiredOutput);
		}
	}
}
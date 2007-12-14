<?php

if ( ! function_exists ( 'mime_content_type' ) )
{
   function mime_content_type ( $f )
   {
       return trim ( exec ('file -bi ' . escapeshellarg ( $f ) ) ) ;
   }
}

define('IMAGE_DERIVATIVE_EXT', 'jpg');

require_once 'Item.php';
require_once 'FileTable.php';
require_once 'FilesImages.php';
require_once 'FilesVideos.php';
require_once 'FileMetaLookup.php';
/**
 * @package Omeka
 * 
 **/
class File extends Omeka_Record { 
    
	public $title = '';
	public $publisher = '';
	public $language = '';
	public $relation = '';
	public $coverage = '';
	public $rights = '';
	public $description = '';
	public $source = '';
	public $subject = '';
	public $creator = '';
	public $additional_creator = '';
	public $date;
	public $added;
	public $modified;
	public $item_id;
	public $format = '';
	public $transcriber = '';
	public $producer = '';
	public $render_device = '';
	public $render_details = '';
	public $capture_date;
	public $capture_device = '';
	public $capture_details = '';
	public $change_history = '';
	public $watermark = '';
	public $authentication = '';
	public $encryption = '';
	public $post_processing = '';
	public $archive_filename;
	public $original_filename;
	public $size = '0';
	public $mime_browser;
	public $mime_os;
	public $type_os;
	public $has_derivative_image = '0';
	public $lookup_id;
	
	
	protected $_related = array('Extended'=>'getExtendedMetadata');
	
	public function __get($name)
	{
		$ext = $this->getExtendedMetadata();

		if($data = $ext[$name]) {
			return $data;
		}
	}
	
	protected function beforeInsert()
	{
		$this->added = date("Y-m-d H:i:s");
		$this->modified = date("Y-m-d H:i:s");		
	}
	
	protected function beforeUpdate()
	{
		$this->modified = date("Y-m-d H:i:s");
	}
	
	protected function beforeSaveForm(&$post)
	{
		$immutable = array(
			'id', 
			'modified', 
			'added', 
			'authentication', 
			'archive_filename', 
			'original_filename', 
			'mime_browser', 
			'mime_os', 
			'type_os');
		foreach ($immutable as $value) {
			unset($post[$value]);
		}
	}
	
	protected function getExtendedMetadata()
	{
		if(!$this->lookup_id) {
			return;
		}
		
		$db = get_db();
		
		$lookupTable = $db->FileMetaLookup;
		$fileTable = $db->File;
		$sql = "SELECT table_name FROM $lookupTable l WHERE l.id = ? LIMIT 1";
		
		//We've got the name of the table that holds the extended data
		$metadataTable = $db->fetchOne($sql, array($this->lookup_id));
		
		$metadataTable = $db->prefix . $metadataTable;
		
		$metadata = $db->query("SELECT d.* FROM $metadataTable d WHERE d.file_id = ? LIMIT 1", array((int) $this->id))->fetch();
		
		$prepared = array();
		
		//We have to unserialize some of these extended metadata values
		foreach ($metadata as $key => $value) {
			
			if($unserialized = @unserialize($value)) {
				$value = $unserialized;
			}
			$prepared[$key] = $value;
		}
				
		return $prepared;
	}
	
	/**
	 * Retrieve the path for the file
	 *
	 * @return string
	 **/
	public function getPath($type='fullsize')
	{
		$fn = $this->getDerivativeFilename();
		
		$path = array('fullsize' => FULLSIZE_DIR.DIRECTORY_SEPARATOR.$fn,
			'thumbnail' => THUMBNAIL_DIR.DIRECTORY_SEPARATOR.$fn,
			'square_thumbnail' => SQUARE_THUMBNAIL_DIR.DIRECTORY_SEPARATOR.$fn,
			'archive' => FILES_DIR.DIRECTORY_SEPARATOR.$this->archive_filename);

		$hookdata = fire_plugin_hook('append_to_file_path', $path);

		if ($hookdata) {
			$path = array_merge($path, $hookdata);
		}
		
		return $path[$type];
	}
	
	/**
	 * Retrieve the web path for the file
	 *
	 * @return void
	 **/
	public function getWebPath($type='fullsize')
	{
		$fn = $this->getDerivativeFilename();

		$path = array('fullsize' => WEB_FULLSIZE.DIRECTORY_SEPARATOR.$fn,
			'thumbnail' => WEB_THUMBNAILS.DIRECTORY_SEPARATOR.$fn,
			'square_thumbnail' => WEB_SQUARE_THUMBNAILS.DIRECTORY_SEPARATOR.$fn,
			'archive' => WEB_FILES.DIRECTORY_SEPARATOR.$this->archive_filename);

		$hookdata = fire_plugin_hook('append_to_file_web_path', $path);

		if ($hookdata) {
			$path = array_merge($path, $hookdata);			
		}
		
		return $path[$type];
	}
	
	public function getDerivativeFilename()
	{
		list($base, $ext) = explode('.', $this->archive_filename);
		$fn = $base.'.'.IMAGE_DERIVATIVE_EXT;
		return $fn;		
	}
	
	public function sanitizeFilename($name)
	{
		//Strip whitespace
		$name = trim($name);
		
		/*	Remove all but last .
			I wish there was an easier way of doing this */
		if(substr_count($name,'.') > 1) {
			$array = explode('.',$name);
			if(count($array) > 2) {
				$last = array_pop($array);
				$first = join('', $array);
				$name = array();
				if(!empty($first)) {
					$name = $first;
				}
				if(!empty($last)) {
					$name .= '.'.$last;
				}
			}
		}
		
		//Strip out invalid characters
		$invalid = array('"','*','/',':','<','>','?','|',"'",'&',';','#','\\');
		$name = str_replace($invalid, '', $name);
		
		//Strip out non-printable characters
		for ($i=0; $i < 32; $i++) { 
			$nonPrintable[$i] = chr($i);
		}
		$name = str_replace($nonPrintable, '', $name);
		
		//Convert to lowercase (avoid corrupting UTF-8)
		$name = strtolower($name);
		
		//Convert remaining spaces to hyphens
		$name = str_replace(' ', '-', $name);
		
		return $name;
	}
	
	public function hasThumbnail()
	{		
		return file_exists($this->getPath('thumbnail'));
	}
	
	public function hasFullsize()
	{
		return file_exists($this->getPath('fullsize'));
	}
	
	/**
	 * Discover all the potential errors for uploaded files before going through the 
	 * arduous process of actually uploading them.
	 *
	 * Throws an error for the first problem it finds.
	 *
	 * @throws Omeka_Upload_Exception
	 * @return void
	 **/
	public function handleUploadErrors($file_form_name)
	{	

		$file_form = $_FILES[$file_form_name];
//		$file_form = $_POST['file'];
		//Check the $_FILES array for errors
		foreach ($file_form['error'] as $key => $error) {
			if($error != UPLOAD_ERR_OK) {
				switch( $error ) {

					// 1 - File exceeds upload size in php.ini
					// 2 - File exceeds upload size set in MAX_FILE_SIZE
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						throw new Omeka_Upload_Exception(
							$_FILES[$file_form_name]['name'][$key] . ' exceeds the maximum file size.' . $_FILES[$file_form_name]['size'][$key]
						);
					break;
					
					// 3 - File partially uploaded
					case UPLOAD_ERR_PARTIAL:
						throw new Omeka_Upload_Exception(
							$_FILES[$file_form_name]['name'][$key] . ' was only partially uploaded.  Please try again.'
						);
					break;
					
					//4 - No file was provided on the form
					case UPLOAD_ERR_NO_FILE:
						//Make sure this doesn't upload and gum up the works, otherwise ignore it
						unset($_FILES[$file_form_name]['error'][$key]);
						unset($_FILES[$file_form_name]['name'][$key]);
						unset($_FILES[$file_form_name]['type'][$key]);
						unset($_FILES[$file_form_name]['tmp_name'][$key]);
						unset($_FILES[$file_form_name]['size'][$key]);
						continue;
						//throw new Omeka_Upload_Exception( 'No file was uploaded!' );
					break;
					
					// 6 - Missing Temp folder
					// 7 - Can't write file to disk
					case UPLOAD_ERR_NO_TMP_DIR:
					case UPLOAD_ERR_CANT_WRITE:
						throw new Omeka_Upload_Exception(
							'There was a problem saving the files to the server.  Please contact an administrator for further assistance.'
						);
					break;
				}				
			}
			
			//Otherwise the file was uploaded correctly, so check to see if it is an image
			if(getimagesize($file_form['tmp_name'][$key])) {
				self::checkOmekaCanMakeDerivativeImages();
			}
		}
		
		//Check whether the POST upload content size is too big
		$POST_MAX_SIZE = ini_get('post_max_size');
		$mul = substr($POST_MAX_SIZE, -1);
		$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
		if ($_SERVER['CONTENT_LENGTH'] > $mul*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
			throw new Omeka_Upload_Exception( 'The size of uploaded files exceeds the maximum size allowed by your hosting provider (' . $POST_MAX_SIZE . ')' );
		}
				
		//Check directory permissions
		//@todo Replace this with a call to the DB to retrieve the paths to the upload directories		
		$writable_directories = array(FILES_DIR, FULLSIZE_DIR, THUMBNAIL_DIR, SQUARE_THUMBNAIL_DIR);
		foreach ($writable_directories as $dir) {
			if (!is_dir($dir)) {
				throw new Omeka_Upload_Exception ("The $dir directory does not exist on the filesystem.  Please create this directory and have a systems administrator");
			}
			if(!is_writable($dir)) {
				throw new Omeka_Upload_Exception ('Unable to write to '. $dir . ' directory; improper permissions');
			}
		}		
	}
	
	protected static function checkOmekaCanMakeDerivativeImages()
	{
		//Check to see if ImageMagick is installed
		if (!self::checkForImageMagick(get_option('path_to_convert'))) {
			throw new Omeka_Upload_Exception( 'ImageMagick is not properly configured.  Please check your settings and then try again.' );
		}		
		
		//Check the constraints to make sure they are valid
		$constraints = array('fullsize_constraint', 'thumbnail_constraint', 'square_thumbnail_constraint');
		
		foreach ($constraints as $constraint) {
			$constraint_size = get_option($constraint);
			
			if(!$constraint_size or !is_numeric($constraint_size)) {
				throw new Omeka_Upload_Exception( 
					"The sizes for derivative images have not been configured properly." );
			}
		}
	}
	
	protected static function checkForImageMagick($path) {
		exec( $path . ' -version', $convert_version, $convert_return );
		return ( $convert_return == 0 );
	}
	
	/**
	 * Stole this jazz from the old File model
	 *
	 * @return void
	 * 
	 **/
	public function upload($form_name, $index) {
		$tmp = $_FILES[$form_name]['tmp_name'][$index];
		$name = $_FILES[$form_name]['name'][$index];
		$originalName = $name;
		$name = $this->sanitizeFilename($name);
		$new_name_string = $this->renameFileForArchive($name);
		$path = FILES_DIR.DIRECTORY_SEPARATOR.$new_name_string;
						
		if( !move_uploaded_file( $tmp, $path ) ) throw new Omeka_Upload_Exception('Could not save file.');	
		
		//set the attributes of this file
		$this->size = $_FILES[$form_name]['size'][$index];
		$this->authentication = md5_file( $path );
		
		$this->mime_browser = $_FILES[$form_name]['type'][$index];
		$this->mime_os = trim( exec( 'file -ib ' . trim( escapeshellarg ( $path ) ) ) );
		$this->type_os = trim( exec( 'file -b ' . trim( escapeshellarg ( $path ) ) ) );

		$this->original_filename = $originalName;
		$this->archive_filename = $new_name_string;
		
		$this->createDerivativeImages($path);
		
		$this->processExtendedMetadata($path);
	}
	
	public function renameFileForArchive($name) {
		$new_name = explode( '.', $name );
		$new_name[0] .= '_' . substr( md5( mt_rand() + microtime( true ) ), 0, 10 );
		$new_name_string = implode( '.', $new_name );
		
		return $new_name_string;
	}

	public function moveToFileDir($oldpath, $name) {
		$name = $this->sanitizeFilename($name);
		$new_name_string = $this->renameFileForArchive($name);
		$path = FILES_DIR.DIRECTORY_SEPARATOR.$new_name_string;
		
		rename($oldpath, $path);

		$this->size = filesize($path);
		$this->authentication = md5_file( $path );

		$this->mime_browser = mime_content_type($path);
		$this->mime_os = trim( exec( 'file -ib ' . trim( escapeshellarg ( $path ) ) ) );
		$this->type_os = trim( exec( 'file -b ' . trim( escapeshellarg ( $path ) ) ) );

		$this->original_filename = $name;
		$this->archive_filename = $new_name_string;
		
		$this->createDerivativeImages($path);
		
		$this->processExtendedMetadata($path);
	}
	
	public function createDerivativeImages($path)
	{
		//Function processes derivatives of every image uploaded - additional images may be created using createImage function.  Additionally, plugin hooks allow you to add your own additional image sizes [DL]
		
		//Retrieve the image sizes from the database
		$full_constraint = get_option('fullsize_constraint');
		$thumb_constraint = get_option('thumbnail_constraint');
		$square_thumbnail_constraint = get_option('square_thumbnail_constraint');
		
		$this->createImage($path, FULLSIZE_DIR, $full_constraint);
		
		$this->createImage($path, THUMBNAIL_DIR, $thumb_constraint);
		
		$this->createImage($path, SQUARE_THUMBNAIL_DIR, $square_thumbnail_constraint, "square");
		
	}

	/**
	 * Process the extended set of metadata for a file (contingent on its MIME type)
	 *
	 * @return bool
	 **/
	public function processExtendedMetadata($path)
	{
		if(!is_readable($path)) {
			throw new Exception( 'File cannot be read!' );
		}
		
		$fi = new FilesImages;
		$m = new FileMetaLookup;
		
		//If we can use the browser mime_type instead of the ID3 extrapolation, do that
		$mime_type = $this->mime_browser;	

		if($this->mimeTypeIsAmbiguous($mime_type)) {
			//If we can't determine MIME type via the browser, 
			//we will pull down ID3 data, but be warned that this may cause a memory error on large files
			$id3 = $this->retrieveID3Info($path);
			$mime_type = $id3->info['mime_type'];
		}
		
		if(!$mime_type) {
			return false;
		}
			
		//Determine the lookup ID from the given MIME type
		$db = get_db();
		$sql = "SELECT * FROM  $db->FileMetaLookup WHERE mime_type = ? LIMIT 1";
		$res = $db->query($sql, array($mime_type))->fetchAll();
				
		//Generate the extended info
		if(count($res)) {
			$this->lookup_id = (int) $res[0]['id'];
			
			//Have the correct class
			$extendedClass = $res[0]['table_class'];
			
			$metadata = new $extendedClass;
					
			if(!isset($id3)) {
				$id3 = $this->retrieveID3Info($path);
			}		

			$info = $id3->info;
			$metadata->generate($info, $path);
			
			$this->Extended = $metadata;
			
			return true;
		}		
		
		return false;
	}
	
	//References a list of ambiguous mime types from "http://msdn2.microsoft.com/en-us/library/ms775147.aspx"
	protected function mimeTypeIsAmbiguous($mime_type)
	{
		return in_array($mime_type, array("text/plain", "application/octet-stream", '', null));
	}
	
	/**
	 * Pull down the file's extra metadata via getID3 library
	 *
	 * @return void
	 **/
	private function retrieveID3Info($path)
	{
		require_once LIB_DIR.DIRECTORY_SEPARATOR.'getid3/getid3.php';
		//Instantiate this third-party sheit
		$id3 = new getID3;
		
		$id3->encoding = 'UTF-8';
		
		try {
			$id3->Analyze($path);
			
			return $id3;
		} catch (Exception $e) {
			return false;
		}		
	}
	
	/**
	 * Save extended metadata if we got it
	 *
	 * @return void
	 **/
	protected function afterSave()
	{
		if($this->Extended instanceof Omeka_Record) {
			$this->Extended->file_id = $this->id;
			$this->Extended->save();
		}
	}
	
	protected function createImage( $old_path, $new_dir, $constraint, $type=null) {
			$convertPath = get_option('path_to_convert');
						
			if (file_exists($old_path) && is_readable($old_path) && getimagesize($old_path)) {	

				$filename = basename( $old_path );
				$new_name = explode( '.', $filename );
				//ensures that all generated files are jpeg
				$new_name[1] = IMAGE_DERIVATIVE_EXT;
				$imagename = implode( '.', $new_name );
				$new_path = rtrim( $new_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $imagename;

				$old_path = escapeshellarg( $old_path );
				$new_path = escapeshellarg( $new_path );

				if(!$constraint) {
					throw new Omeka_Upload_Exception('Image creation failed - Image size constraint must be specified within application settings');
				}

				switch ($type) {
				case "square":
					$command = ''.$convertPath.' '.$old_path.' -thumbnail x'.($constraint*2).' -resize "'.($constraint*2).'x<" -resize 50% -gravity center -crop '.$constraint.'x'.$constraint.'+0+0 +repage '.$new_path.'';
					break;
				default:
					$command = ''.$convertPath.' '.$old_path.' -resize '.escapeshellarg($constraint.'x'.$constraint.'>').' '.$new_path.'';						
				}

				exec( $command, $result_array, $result_value );

				if ($result_value == 0) {
					//Image was created, so set the derivative bitflag
					if(!$this->has_derivative_image) {
						$this->has_derivative_image = 1;
					}

					return $imagename;	
				} else {
					throw new Omeka_Upload_Exception('Something went wrong with image creation.  Please notify an administrator.');
				}
			}
	}
	
	public function unlinkFile() {
		$files = array( 
			$this->getPath('fullsize'), 
			$this->getPath('thumbnail'), 
			$this->getPath('archive'),
			$this->getPath('square_thumbnail') );
		
		foreach( $files as $file )
		{
			if( file_exists($file) && !is_dir($file) ) unlink($file);
		}
	}
	
	protected function _delete() {
		$this->unlinkFile();
	}
}  	 

?>
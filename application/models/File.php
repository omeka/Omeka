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
	 * Retrieve the path for the image
	 *
	 * @return string
	 **/
	public function getPath($type='fullsize')
	{
		$fn = $this->getDerivativeFilename();
		switch ($type) {
			case 'fullsize':
				return FULLSIZE_DIR.DIRECTORY_SEPARATOR.$fn;
				break;
			case 'thumbnail':
				return THUMBNAIL_DIR.DIRECTORY_SEPARATOR.$fn;
			case 'square_thumbnail':
				return SQUARE_THUMBNAIL_DIR.DIRECTORY_SEPARATOR.$fn;
			case 'archive':
			default:
				return FILES_DIR.DIRECTORY_SEPARATOR.$this->archive_filename;
				break;
		}
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
	 * Stole this jazz from the old File model
	 *
	 * @return void
	 * 
	 **/
	public function upload($form_name, $index, $useExif = false) {
		$error = $_FILES[$form_name]['error'][$index];

		$POST_MAX_SIZE = ini_get('post_max_size');
		$mul = substr($POST_MAX_SIZE, -1);
		$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
		if ($_SERVER['CONTENT_LENGTH'] > $mul*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) $error = true;
		

		if( $error == UPLOAD_ERR_OK ) {
				$tmp = $_FILES[$form_name]['tmp_name'][$index];
				$name = $_FILES[$form_name]['name'][$index];
				$originalName = $name;
				$name = $this->sanitizeFilename($name);
				$new_name = explode( '.', $name );
				$new_name[0] .= '_' . substr( md5( mt_rand() + microtime( true ) ), 0, 10 );
				$new_name_string = implode( '.', $new_name );
				$path = FILES_DIR.DIRECTORY_SEPARATOR.$new_name_string;
				
				if( !is_writable(dirname($path)) )
				{
					throw new Exception ('Unable to write to '. dirname($path) . ' directory; improper permissions');
				}
				
				if( !move_uploaded_file( $tmp, $path ) ) throw new Exception('Could not save file.');
				
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
		} else {
				switch( $error ) {

					// 1 - File exceeds upload size in php.ini
					// 2 - File exceeds upload size set in MAX_FILE_SIZE
					case UPLOAD_ERR_INI_SIZE:
					case UPLOAD_ERR_FORM_SIZE:
						throw new Exception(
							$_FILES[$file_form_name]['name'][$key] . ' exceeds the maximum file size.' . $_FILES[$file_form_name]['size'][$key]
						);
					break;
					
					// 3 - File partially uploaded
					case UPLOAD_ERR_PARTIAL:
						throw new Exception(
							$_FILES[$file_form_name]['name'][$key] . ' was only partially uploaded.  Please try again.'
						);
					break;
					
					//
					case UPLOAD_ERR_NO_FILE:
						throw new Exception( 'No file was uploaded!' );
					break;
					
					// 6 - Missing Temp folder
					// 7 - Can't write file to disk
					case UPLOAD_ERR_NO_TMP_DIR:
					case UPLOAD_ERR_CANT_WRITE:
						throw new Exception(
							'There was a problem saving the files to the server.  Please contact an administrator for further assistance.'
						);
					break;
				}
		}
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

	protected function processExtendedMetadata($path)
	{
		$fi = new FilesImages;
		$m = new FileMetaLookup;
		
		require_once 'getid3/getid3.php';
		//Instantiate this third-party sheit
		$id3 = new getID3;
		
		$id3->encoding = 'UTF-8';
		
		try {
			$id3->Analyze($path);
		} catch (Exception $e) {
			return false;
		}
		
		$mime_type = $id3->info['mime_type'];
		
		//Get the lookup ID for the correct table
		$db = get_db();
		$sql = "SELECT * FROM  $db->FileMetaLookup WHERE mime_type = ? LIMIT 1";
		$res = $db->query($sql, array($mime_type))->fetchAll();
				
		//Generate the extended info
		if(count($res)) {
			$this->lookup_id = $res[0]['id'];
			//Have the correct class
			$extendedClass = $res[0]['table_class'];
			
			$metadata = new $extendedClass;
					
			$info = $id3->info;
			$metadata->generate($info, $path);
			
			$this->Extended = $metadata;
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
	
	protected function checkImage( $new_dir, $old_path, $convertPath) {
		
		if (!$this->checkForImageMagick($convertPath)) {
			throw new Exception( 'ImageMagick library is required for thumbnail generation' );
		}
		
		if (!is_dir($new_dir)) {
			throw new Exception ('Invalid directory to put new image');
		}
		if (!is_writable($new_dir)) {
			throw new Exception ('Unable to write to '. $new_dir . ' directory; improper permissions');
		}
	}

	protected function createImage( $old_path, $new_dir, $constraint, $type=null) {
			$convertPath = get_option('path_to_convert');
			
			$this->checkImage( $new_dir, $old_path, $convertPath);
			
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
					throw new Exception('Image creation failed - Image size constraint must be specified within application settings');
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
					throw new Exception('Something went wrong with image creation.  Ensure that the thumbnail directories have appropriate write permissions.');
				}
			}
	}
	
	protected function checkForImageMagick($path) {
		exec( $path . ' -version', $convert_version, $convert_return );
		return ( $convert_return == 0 );
	}
	
	public function unlinkFile() {
		$files = array( 
			$this->getPath('fullsize'), 
			$this->getPath('thumbnail'), 
			$this->getPath('archive') );
		
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
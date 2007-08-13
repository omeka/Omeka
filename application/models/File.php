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
require_once 'FilesImages.php';
require_once 'FilesVideos.php';
require_once 'FileMetaLookup.php';
/**
 * @package Omeka
 * 
 **/
class File extends Kea_Record { 
    
	protected $extendedMetadata = array();
	
	public function setUp() {
//		Removed [5-22-07 KBK], this throws errors when attempting to delete files from items/form		
//		$this->hasOne("Item", "File.item_id");
		$this->hasOne('FileMetaLookup', 'File.lookup_id');
		$this->hasOne('FilesImages', 'FilesImages.file_id');
		$this->hasOne('FilesVideos', 'FilesVideos.file_id');		
	}

	public function setTableDefinition() {
		$this->option('type', 'MYISAM');
		$this->setTableName('files');
		
       	$this->hasColumn('title', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('publisher', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('language', 'string', 40, array('notnull' => true, 'default'=>''));
        $this->hasColumn('relation', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('coverage', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('rights', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('description', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('source', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('subject', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('creator', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('additional_creator', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('date', 'date', null);
        $this->hasColumn('added', 'timestamp', null);
        $this->hasColumn('modified', 'timestamp', null);
        $this->hasColumn('item_id', 'integer', null, array('range'=>array('1')));
        $this->hasColumn('format', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('transcriber', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('producer', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('render_device', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('render_details', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('capture_date', 'timestamp', null);
        $this->hasColumn('capture_device', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('capture_details', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('change_history', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('watermark', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('authentication', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('encryption', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('compression', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('post_processing', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('archive_filename', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('original_filename', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('size', 'integer', null, array('default'=>'0', 'notnull' => true));
        $this->hasColumn('mime_browser', 'string');
        $this->hasColumn('mime_os', 'string');
        $this->hasColumn('type_os', 'string');
		
		$this->hasColumn('has_derivative_image', 'boolean', null, array('default'=>'0', 'notnull'=>true));
		
        $this->hasColumn('lookup_id', 'integer');
		
		$this->index('item', array('fields' => array('item_id')));
    }
	
	public function get($name)
	{
		if($this->hasRelation($name)) {
			return parent::get($name);
		}else {
			//Try to obtain extended metadata
			if(empty($this->extendedMetadata)) {
				$this->extendedMetadata = $this->getExtendedMetadata();
			}
			$metadata = $this->extendedMetadata[$name];
			
			if($unserialized = unserialize($metadata)) {
				return $unserialized;
			}else {
				return $metadata;
			}
			
		}
	}
	
	protected function preCommitForm(&$post, $options)
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
		$lookupTable = $this->getTableName('FileMetaLookup');
		$fileTable = $this->getTableName();
		$sql = "SELECT table_name FROM $lookupTable l WHERE l.id = {$this->lookup_id}";
		
		//We've got the name of the table that holds the extended data
		$metadataTable = $this->execute($sql, array(), true);
		
		
		$metadata = $this->execute("SELECT d.* FROM $metadataTable d WHERE d.file_id = ?", array($this->id));
		return $metadata[0];
	}
	
	public function isPublic()
	{
		$sql = "SELECT COUNT(*) FROM items i WHERE i.id = ? AND i.public = 1";
		$count = $this->execute($sql,array($this->item_id), true);
		return ($count > 0);
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
				
				//Retrieve the image sizes from the database
				$full_constraint = get_option('fullsize_constraint');
				$thumb_constraint = get_option('thumbnail_constraint');
				
				$this->createImage(FULLSIZE_DIR, $path, $full_constraint);
				
				$this->createImage(THUMBNAIL_DIR, $path, $thumb_constraint);
				
				$this->processExtendedMetadata($path);
		} else {
			// Ignore error '4' - no file uploaded and error '0' - file uploaded correctly
				switch( $error ) {

					// 1 - File exceeds upload size in php.ini
					// 2 - File exceeds upload size set in MAX_FILE_SIZE
					case( '1' ):
					case( '2' ):
						throw new Exception(
							$_FILES[$file_form_name]['name'][$key] . ' exceeds the maximum file size.' . $_FILES[$file_form_name]['size'][$key]
						);
					break;
					
					// 3 - File partially uploaded
					case( '3' ):
						throw new Exception(
							$_FILES[$file_form_name]['name'][$key] . ' was only partially uploaded.  Please try again.'
						);
					break;
					
					// 6 - Missing Temp folder
					// 7 - Can't write file to disk
					case( '6' ):
					case( '7' ):
						throw new Exception(
							'There was a problem saving the files to the server.  Please contact an administrator for further assistance.'
						);
					break;
				}
		}
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
		$lookupTable = $this->getTableName('FileMetaLookup');
		$sql = "SELECT * FROM $lookupTable WHERE mime_type = ? LIMIT 1";
		$res = $this->execute($sql, array($mime_type));
		
		//Generate the extended info
		if(count($res)) {
			$this->lookup_id = $res[0]['id'];
			//Have the correct class
			$extendedClass = $res[0]['table_class'];
			$info = $id3->info;
			$this->$extendedClass->generate($info, $path);
		}		
	}
	
	/**
	 * Also ripped off/modded from old File model
	 *
	 * @return void
	 * 
	 **/
	protected function createImage( $new_dir, $old_path, $constraint) {
		
		$convertPath = get_option('path_to_convert');
		
		if (!$this->checkForImageMagick($convertPath)) {
			//throw new Exception( 'ImageMagick library is required for thumbnail generation' );
			return null;
		}
		
		if (!is_dir($new_dir)) {
			throw new Exception ('Invalid directory to put new image');
		}
		if (!is_writable($new_dir)) {
			throw new Exception ('Unable to write to '. $new_dir . ' directory; improper permissions');
		}
		
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
			
			$command = ''.$convertPath.' '.$old_path.' -resize '.escapeshellarg($constraint.'x'.$constraint.'>').' '.$new_path.'';
			
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
	
	private function checkForImageMagick($path) {
		exec( $path . ' -version', $convert_version, $convert_return );
		return ( $convert_return == 0 );
	}
	
	protected function deleteFiles() {
		$files = array( 
			$this->getPath('fullsize'), 
			$this->getPath('thumbnail'), 
			$this->getPath('archive') );
		
		foreach( $files as $file )
		{
			if( file_exists($file) && !is_dir($file) ) unlink($file);
		}
	}
	public function delete() {
		$this->deleteFiles();
		parent::delete();
	}
}  	 

?>
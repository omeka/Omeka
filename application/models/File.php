<?php

if ( ! function_exists ( 'mime_content_type' ) )
{
   function mime_content_type ( $f )
   {
       return trim ( exec ('file -bi ' . escapeshellarg ( $f ) ) ) ;
   }
}

require_once 'Item.php';

/**
 * @package Omeka
 * 
 **/
class File extends Kea_Record { 
    

	public function setUp() {
//		Removed [5-22-07 KBK], this throws errors when attempting to delete files from items/form		
//		$this->hasOne("Item", "File.item_id");		
	}

	public function setTableDefinition() {
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
        $this->hasColumn('item_id', 'integer');
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
        $this->hasColumn('fullsize_filename', 'string');
        $this->hasColumn('original_filename', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('thumbnail_filename', 'string');
        $this->hasColumn('size', 'integer', null, array('default'=>'0', 'notnull' => true));
        $this->hasColumn('mime_browser', 'string', null, array('default'=>''));
        $this->hasColumn('mime_php', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('mime_os', 'string', null, array('notnull' => true, 'default'=>''));
        $this->hasColumn('type_os', 'string', null, array('notnull' => true, 'default'=>''));
		
		$this->index('item', array('fields' => array('item_id')));
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
		switch ($type) {
			case 'fullsize':
				return FULLSIZE_DIR.DIRECTORY_SEPARATOR.$this->fullsize_filename;
				break;
			case 'thumbnail':
				return THUMBNAIL_DIR.DIRECTORY_SEPARATOR.$this->thumbnail_filename;
			case 'archive':
			default:
				return FILES_DIR.DIRECTORY_SEPARATOR.$this->archive_filename;
				break;
		}
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
		return !empty($this->thumbnail_filename);
	}
	
	public function hasFullsize()
	{
		return !empty($this->fullsize_filename);
	}
	
	/**
	 * Stole this jazz from the old File model
	 *
	 * @return void
	 * 
	 **/
	public function upload($form_name, $index, $useExif = false) {
		$error = $_FILES[$form_name]['error'][$index];

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
				$this->mime_php = mime_content_type( $path );
				$this->mime_os = trim( exec( 'file -ib ' . trim( escapeshellarg ( $path ) ) ) );
				$this->type_os = trim( exec( 'file -b ' . trim( escapeshellarg ( $path ) ) ) );

				$this->original_filename = $originalName;
				$this->archive_filename = $new_name_string;
				
				//Retrieve the image sizes from the database
				$full_constraint = get_option('fullsize_constraint');
				$thumb_constraint = get_option('thumbnail_constraint');
				
				$this->fullsize_filename = $this->createImage(FULLSIZE_DIR, $path, $full_constraint );
				
				$this->thumbnail_filename = $this->createImage(THUMBNAIL_DIR, $path, $thumb_constraint );
				
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
	
	/**
	 * Also ripped off/modded from old File model
	 *
	 * @return void
	 * 
	 **/
	protected function createImage( $new_dir, $old_path, $constraint, $no_enlarge = true ) {
		$convertPath = get_option('path_to_convert');
		
		if(!$this->checkForImageMagick($convertPath)) {
			throw new Exception( 'ImageMagick library is required for thumbnail generation' );
		}
		
		if( !is_dir($new_dir) )
		{
			throw new Exception ('Invalid directory to put new image');
		}
		if( !is_writable($new_dir) )
		{
			throw new Exception ('Unable to write to '. $new_dir . ' directory; improper permissions');
		}
		
		if( file_exists( $old_path ) && is_readable( $old_path ) && getimagesize( $old_path ) )
		{	
			list( $width, $height, $type ) = getimagesize( $old_path );
			
			$filename = basename( $old_path );
			$new_name = explode( '.', $filename );
			$new_name[0] .= '_' . basename($new_dir);
			//ensures that all generated files are jpeg
			$new_name[1] = 'jpg';
			$imagename = implode( '.', $new_name );
			$new_path = rtrim( $new_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $imagename;
			
			$old_path = escapeshellarg( $old_path );
			$new_path = escapeshellarg( $new_path );
			
			if(!$constraint) {
				throw new Exception( 
					'Image creation failed - Image size constraint must be specified within application settings' 
				);
			}
			
			//Landscape aspect-ratio
			if($width > $height) {
				//Width is the constraint
				
				$new_width = $constraint;
				$new_height = ($constraint / $width) * $height;
			}
			//Portrait aspect-ratio
			else {
				//Height is the constraint

				$new_height = $constraint;
				$new_width = ($constraint / $height) * $width;
			}
			
			$command = $convertPath . ' ' . $old_path . ' -resize \'' . $new_width . 'x' . $new_height . '>\' ' . $new_path;
			
			//We probably don't want to make images that are any bigger than the raw file
			if( $no_enlarge )
			{
				if ( ( $new_width > $width ) || ( $new_height > $height ) )
				{
					$command = $convertPath . ' ' . $old_path . ' ' . $new_path;
				}
			}

			exec( $command, $result_array, $result_value );
			if( $result_value == 0 )
			{
				return $imagename;	
			}
			else
			{
			
				throw new Exception(
					'Something went wrong with image creation.  Ensure that the thumbnail directories have appropriate write permissions.'
				);
			}
		}
	}
	
	private function checkForImageMagick($path) {
		exec( $path . ' -version', $convert_version, $convert_return );
		return ( $convert_return == 0 );
	}
	
	protected function deleteFiles() {
		$files = array( 
			(FULLSIZE_DIR . DIRECTORY_SEPARATOR . $this->fullsize_filename), 
			(THUMBNAIL_DIR . DIRECTORY_SEPARATOR . $this->thumbnail_filename), 
			(FILES_DIR . DIRECTORY_SEPARATOR . $this->archive_filename) );
		
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
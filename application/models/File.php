<?php

define('ARCHIVE_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'archive');
define('THUMBNAIL_DIR', ARCHIVE_DIR.DIRECTORY_SEPARATOR.'thumbnails');
define('FULLSIZE_DIR', ARCHIVE_DIR.DIRECTORY_SEPARATOR.'fullsize');
define('FILES_DIR', ARCHIVE_DIR.DIRECTORY_SEPARATOR.'files');
define('FULLSIZE_IMAGE_WIDTH', 600);
define('THUMBNAIL_IMAGE_WIDTH', 150);
define( 'PATH_TO_CONVERT', '/usr/bin/convert' );

require_once 'Item.php';

/**
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class File extends Kea_Record { 
    

	public function setUp() {
		$this->hasOne("Item", "File.item_id");
	}

	public function setTableDefinition() {
		$this->setTableName('files');
		
        $this->hasColumn("title","string",400);
		$this->hasColumn("publisher","string",400);
		$this->hasColumn("language","string",40);
		$this->hasColumn("relation","string",null);
		$this->hasColumn("coverage","string",null);
		$this->hasColumn("rights","string",null);
		$this->hasColumn("description","string", null);
		$this->hasColumn("source","string",null);
		$this->hasColumn("subject","string",400);
		$this->hasColumn("creator","string",400);
		$this->hasColumn("additional_creator","string",400);
		$this->hasColumn("date","date");
		$this->hasColumn("added","timestamp");
		$this->hasColumn("modified","timestamp");
		$this->hasColumn("item_id","integer");	 
		$this->hasColumn("transcriber","string",null);
		$this->hasColumn("producer","string",null);
		$this->hasColumn("render_device","string",null);
		$this->hasColumn("render_details","string",null);
		$this->hasColumn("capture_date", "timestamp");
		$this->hasColumn("capture_device","string",null);
		$this->hasColumn("capture_details", "string",null);
		$this->hasColumn("change_history","string",null);
		$this->hasColumn("watermark","string",null);
		$this->hasColumn("authentication","string",null);
		$this->hasColumn("encryption", "string",null);
		$this->hasColumn("compression", "string",null);
		$this->hasColumn("post_processing","string",null);
		$this->hasColumn("archive_filename","string",400);
		$this->hasColumn("fullsize_filename","string",400);
		$this->hasColumn("original_filename","string",400);
		$this->hasColumn("thumbnail_filename","string",400);
		$this->hasColumn("size","integer");
		$this->hasColumn("mime_browser","string",400);
		$this->hasColumn("mime_php","string",400);
		$this->hasColumn("mime_os","string",400);
		$this->hasColumn("type_os","string",400);
    }

	/**
	 * Stole this jazz from the old File model
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	public function upload($form_name, $index, $useExif = false) {
		$error = $_FILES[$form_name]['error'][$index];

		if( $error == UPLOAD_ERR_OK ) {
				$tmp = $_FILES[$form_name]['tmp_name'][$index];
				$name = $_FILES[$form_name]['name'][$index];
				$new_name = explode( '.', $name );
				$new_name[0] .= '_' . substr( md5( mt_rand() + microtime( true ) ), 0, 10 );
				$new_name_string = implode( '.', $new_name );
				$path = FILES_DIR.DIRECTORY_SEPARATOR.$new_name_string;
				
				if( !move_uploaded_file( $tmp, $path ) ) throw new Exception('Could not save file.');
				
				//set the attributes of this file
				$this->size = $_FILES[$form_name]['size'][$index];
				$this->authentication = md5_file( $path );
				
				$this->mime_browser = $_FILES[$file_form_name]['type'][$index];
				$this->mime_php = mime_content_type( $path );
				$this->mime_os = trim( exec( 'file -ib ' . trim( escapeshellarg ( $path ) ) ) );
				$this->type_os = trim( exec( 'file -b ' . trim( escapeshellarg ( $path ) ) ) );

				$this->original_filename = $name;
				$this->archive_filename = $new_name_string;
				$this->fullsize_filename = $this->createImage(FULLSIZE_DIR, $path, null, FULLSIZE_IMAGE_WIDTH );
				
				$this->thumbnail_filename = $this->createImage(THUMBNAIL_DIR, $path, null, THUMBNAIL_IMAGE_WIDTH );
				
		}
	}
	
	/**
	 * Also ripped off/modded from old File model
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	protected function createImage( $new_dir, $old_path, $percent = null, $new_width = null, $new_height = null, $output = 3, $no_enlarge = true ) {
		if(!$this->checkForImageMagick()) {
			throw new Exception( 'ImageMagick library is required for thumbnail generation' );
		}
		
		if( !is_dir($new_dir) )
		{
			throw new Exception ('Invalid directory to put new image');
		}
		if( !is_writeable($new_dir) )
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
			
			if( $percent )
			{
				$new_width = ($percent * $width);
				$new_height = ($percent * $height);
				
				// This is the actual convert command
				$command = PATH_TO_CONVERT . ' ' . $old_path . ' -resize ' . $percent . '% ' . $new_path;
			}
			elseif( $new_width && $new_height )
			{
				$command = PATH_TO_CONVERT . ' ' . $old_path . ' -resize ' . $new_width . 'x' . $new_height . '> ' . $new_path;
			}
			elseif( $new_width && !$new_height )
			{
				$command = PATH_TO_CONVERT . ' ' . $old_path . ' -resize ' . $new_width . 'x ' . $new_path;
			}
			elseif( !$new_width && $new_height )
			{
				$command = PATH_TO_CONVERT . ' ' . $old_path . ' -resize ' . 'x' . $new_height . ' ' . $new_path;
			}
			elseif( !$percent && !$new_width && !$new_height )
			{
				throw new Exception(
					'At least one of the following must be specified: percent, new width, new height or both new width and new height.'
				);
			}
			
			//We probably don't want to make images that are any bigger than the raw file
			if( $no_enlarge )
			{
				if ( ( !empty($new_width) && $new_width > $width ) || ( !empty($new_height) && $new_height > $height ) )
				{
					$command = PATH_TO_CONVERT . ' ' . $old_path . ' ' . $new_path;
				}
			}

			exec( $command, $result_array, $result_value );
			
			if( $result_value == 0 )
			{
				return $imagename;	
			}
			else
			{
				throw new Kea_Domain_Exception(
					'Something went wrong with thumbnail creation.  Ensure that the thumbnail directories have appropriate write permissions.'
				);
			}
		}
	}
	
	private function checkForImageMagick() {
		exec( PATH_TO_CONVERT . ' -version', $convert_version, $convert_return );
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
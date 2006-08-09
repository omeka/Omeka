<?php

class File extends Kea_Domain_Model
{
	public $file_id;
	public $file_title;
	public $file_publisher;
	public $file_language;
	public $file_relation;
	public $file_coverage_start;
	public $file_coverage_end;
	public $file_rights;
	public $file_description;
	public $file_date;
	public $object_id;
	public $contributor_id;
	
	public $file_transcriber;
	public $file_producer;
	public $file_render_device;
	public $file_capture_date;
	public $file_capture_device;
	public $file_change_history;
	public $file_watermark;
	public $file_authentication;
	public $file_encryption;
	public $file_compression;
	public $file_post_processing;
	
	public $file_size;
	public $file_mime_browser;
	public $file_mime_php;
	public $file_mime_os;
	public $file_type_os;
	public $file_archive_filename;
	public $file_original_filename;
	public $file_thumbnail_name;
	public $file_added;
	
	/**
	 *	
	 */
	public function validate() {}
	
	public static function delete( $file_id )
	{
		$inst = new self;
		$mapper = $inst->mapper();
		$file = $mapper->find()
					   ->where( 'file_id = ?', $file_id )
					   ->execute();
		$filename = ABS_VAULT_DIR . DIRECTORY_SEPARATOR . $file->file_archive_filename;
		if( file_exists( $filename ) )
		{
			unlink( $filename );
		}
		if( $file->file_thumbnail_name )
		{
			$thumbnail_path = ABS_THUMBNAIL_DIR . DIRECTORY_SEPARATOR . $file->file_thumbnail_name;
			if( file_exists( $thumbnail_path ) )
			{
				unlink( $thumbnail_path );
			}
		}			
		return $mapper->delete( $file_id );
	}
	
	public static function deleteUnsaved( File $file )
	{
		$filename = ABS_VAULT_DIR . DIRECTORY_SEPARATOR . $file->file_archive_filename;
		if( file_exists( $filename ) )
		{
			unlink( $filename );
		}
		if( $file->file_thumbnail_name )
		{
			$thumbnail_path = ABS_THUMBNAIL_DIR . DIRECTORY_SEPARATOR . $file->file_thumbnail_name;
			if( file_exists( $thumbnail_path ) )
			{
				unlink( $thumbnail_path );
			}
		}
	}
	
	public static function add( $obj_id = null, $contributor_id = null, $file_form_name = null )
	{
		
		// Bump up memory allocated to PHP so there's space to manipulate big files
		ini_set('memory_limit', '64M');
		
		if( isset( $_FILES[$file_form_name] ) ) {

			$files = array();
			
			foreach( $_FILES[$file_form_name]['error'] as $key => $error ) {

				// Ignore error '4' - no file uploaded and error '0' - file uploaded correctly
				switch( $error ) {

					// 1 - File exceeds upload size in php.ini
					// 2 - File exceeds upload size set in MAX_FILE_SIZE
					case( '1' ):
					case( '2' ):
						throw new Kea_Domain_Exception(
							$_FILES[$file_form_name]['name'][$key] . ' exceeds the maximum file size.' . $_FILES[$file_form_name]['size'][$key]
						);
					break;
					
					// 3 - File partially uploaded
					case( '3' ):
						throw new Kea_Domain_Exception(
							$_FILES[$file_form_name]['name'][$key] . ' was only partially uploaded.  Please try again.'
						);
					break;
					
					// 6 - Missing Temp folder
					// 7 - Can't write file to disk
					case( '6' ):
					case( '7' ):
						throw new Kea_Domain_Exception(
							'There was a problem saving the files to the server.  Please contact an administrator for further assistance.'
						);
					break;
				}
			}
			
			foreach( $_FILES[$file_form_name]['error'] as $key => $error ) {
				
				// Ignore the files fields that didn't get uploaded
				if( $error == UPLOAD_ERR_OK ) {
					$tmp = $_FILES[$file_form_name]['tmp_name'][$key];
					$name = $_FILES[$file_form_name]['name'][$key];
					$new_name = explode( '.', $name );
					$new_name[0] .= '_' . substr( md5( mt_rand() + microtime( true ) ), 0, 10 );
					$new_name_string = implode( '.', $new_name );
					
					$new_path = ABS_VAULT_DIR . DIRECTORY_SEPARATOR . $new_name_string;

					if( move_uploaded_file( $tmp, $new_path ) ) {
						//save the file to the db
						$mime_php = mime_content_type( $new_path );
						$mime_os = trim( exec( 'file -ib ' . trim( escapeshellarg ( $new_path ) ) ) );
						$file_type = trim( exec( 'file -b ' . trim( escapeshellarg ( $new_path ) ) ) );
						
						$file_array = array(	'object_id'					=> $obj_id,
												'file_size'					=> $_FILES[$file_form_name]['size'][$key],
												'file_mime_browser'			=> $_FILES[$file_form_name]['type'][$key],
												'file_authentication'		=> md5_file( $new_path ),
												'file_mime_php'				=> $mime_php,
												'file_mime_os'				=> $mime_os,
												'file_type_os'				=> $file_type,
												'file_archive_filename'		=> $new_name_string,
												'file_original_filename'	=> $name,
												'file_thumbnail_name'		=> self::createThumbnail( $new_path, null, 140 ) );				
						$file = new File( $file_array );
						if( $contributor_id )
						{
							$file->contributor_id = $contributor_id;
						}
						if( $obj_id )
						{
							$file->save();
						}
						$files[] = $file;
					} else {
						throw new Kea_Domain_Exception(
							'Could not save the file.'
						);
					}
				}
			}
			return $files;
		}
		return false;
	}
	
	public static function createThumbnail( $file, $percent = null, $new_width = null, $new_height = null, $output = 3 )
	{
		exec( PATH_TO_CONVERT . ' -version', $convert_version, $convert_return );
		if( $convert_return == 0 )
		{
			return self::imCreateThumbnail( $file, $percent, $new_width, $new_height );
		}
		elseif( function_exists( 'gd_info' ) )
		{
			return self::gdCreateThumbnail( $file, $percent, $new_width, $new_height, $output );
		}
		else
		{
			throw new Kea_Domain_Exception(
				'Neither the GD Library nor Imagemagick is available to process thumbnails.'
			);
		}
	}
	
	public static function gdCreateThumbnail( $file, $percent = null, $new_width = null, $new_height = null, $output = 3 )
	{

		if( !function_exists( 'gd_info' ) ) {
			throw new Kea_Domain_Exception(
				'The GD Library is not installed and is required for this method.'
			);
			return false;
		}
		
		if( file_exists( $file ) && is_readable( $file ) && getimagesize( $file ) ) {
			
			list( $width, $height, $type ) = getimagesize( $file );

			if( $percent ) {
				$new_width = ($percent * $width);
				$new_height = ($percent * $height);
			} elseif( $new_width && !$new_height ) {
				if( $new_width < $width ) {
					$ratio = ( $new_width / $width );
					$new_height = ( $height * $ratio );
				} else {
					$new_width = $width;
					$new_height = $height;
				}
			} elseif( !$new_width && $new_height ) {
				if( $new_height < $height ) {
					$ratio = ( $new_height / $height );
					$new_width = ( $width * $ratio );					
				} else {
					$new_height = $height;
					$new_width = $width;
				}
			} elseif( !$percent && !$new_width && !$new_height ) {
				throw new Kea_Domain_Exception(
					'At least one of the following must be specified: percent, new width, new height or both new width and new height.'
				);
			}

			$thumb = imagecreatetruecolor( $new_width, $new_height );
			
			// From php.net, with a few bug fixes and a MAX_MEMORY setting
			// This function should probably go somewhere else
			function setMemoryForImage($filename)
			{
				// DEBUG
				$debugEmail = 'dstillman@chnm.gmu.edu';
				
				$imageInfo = getimagesize($filename);
				$MAX_MEMORY = 45; // maximum MB to use -- set this as low as possible, and not above 50!
				$MB = 1048576;  // number of bytes in 1M
				$K64 = 65536;    // number of bytes in 64K
				$TWEAKFACTOR = 1.7;  // adjust as necessary
				$memoryNeeded = round( ( $imageInfo[0] * $imageInfo[1]
						* $imageInfo['bits']
						* $imageInfo['channels'] / 8
						+ $K64
						) * $TWEAKFACTOR
					);
				
				// DEBUG
				mail($debugEmail, 'mem', $memoryNeeded);
				
				// Retrieve value from php.ini and trim trailing 'M'
				// ini_get('memory_limit') only works if compiled with "--enable-memory-limit"
				$memoryLimitMB = substr(ini_get('memory_limit'), 0, -1);
				$memoryLimit = $memoryLimitMB * $MB;
				if (function_exists('memory_get_usage') &&
						memory_get_usage() + $memoryNeeded > $memoryLimit){
					$newLimit = $memoryLimitMB + ceil( ( memory_get_usage()
						+ $memoryNeeded
						- $memoryLimit
						) / $MB
					);
					
					if ($newLimit > $MAX_MEMORY){
						// DEBUG
						mail($debugEmail, 'greater than max', '');
						
						return false;
					}
					
					// DEBUG
					mail($debugEmail, 'allocating', $newLimit);
					
					ini_set('memory_limit', $newLimit . 'M');
					return true;
				} else {
					// DEBUG
					mail($debugEmail, 'not allocating', '');
					
					return false;
				}
			}
			setMemoryForImage($file);
			
			switch( $type ) {
				//GIF
				case( '1' ):
					$original = imagecreatefromgif( $file );
				break;
				//JPG
				case( '2' ):
					$original = imagecreatefromjpeg( $file );
				break;
				//PNG
				case( '3' ):
					$original = imagecreatefrompng( $file );
				break;
				default:
					return false;
				break;
			}

			imagecopyresampled( $thumb, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

			$filename = basename( $file );
			$new_name = explode( '.', $filename );
			$new_name[0] .= '_thumb';
			$thumbname = implode( '.', $new_name );
			
			$new_path = rtrim( ABS_THUMBNAIL_DIR, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $thumbname;

			switch( $output ) {
				//GIF
				case( '1' ):
					imagegif( $thumb, $new_path );
				break;
				//JPG
				case( '2' ):
					imagejpeg( $thumb, $new_path, 100 );
				break;
				//PNG
				case( '3' ):
					imagepng( $thumb, $new_path );
				break;
				default:
					throw new Kea_Domain_Exception(
						'Can only encode thumbnails as png, jpeg, or gif.'
					);
				break;
			}
			imagedestroy( $original );
			imagedestroy( $thumb );
			return $thumbname;
		}
		return false;
	}
	
	public static function imCreateThumbnail( $file, $percent = null, $new_width = null, $new_height = null )
	{

		if( file_exists( $file ) && is_readable( $file ) && getimagesize( $file ) )
		{	
			list( $width, $height, $type ) = getimagesize( $file );
			
			$filename = basename( $file );
			$new_name = explode( '.', $filename );
			$new_name[0] .= '_thumb';
			$thumbname = implode( '.', $new_name );
			
			$new_path = rtrim( ABS_THUMBNAIL_DIR, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $thumbname;
			
			$file = escapeshellarg( $file );
			$new_path = escapeshellarg( $new_path );

			if( $percent )
			{
				$new_width = ($percent * $width);
				$new_height = ($percent * $height);
				
				// This is the actual convert command
				$command = PATH_TO_CONVERT . ' ' . $file . ' -resize ' . $percent . '% ' . $new_path;
			}
			elseif( $new_width && $new_height )
			{
				$command = PATH_TO_CONVERT . ' ' . $file . ' -resize ' . $new_width . 'x' . $new_height . '> ' . $new_path;
			}
			elseif( $new_width && !$new_height )
			{
				$command = PATH_TO_CONVERT . ' ' . $file . ' -resize ' . $new_width . 'x ' . $new_path;
			}
			elseif( !$new_width && $new_height )
			{
				$command = PATH_TO_CONVERT . ' ' . $file . ' -resize ' . 'x' . $new_height . ' ' . $new_path;
			}
			elseif( !$percent && !$new_width && !$new_height )
			{
				throw new Kea_Domain_Exception(
					'At least one of the following must be specified: percent, new width, new height or both new width and new height.'
				);
			}

			exec( $command, $result_array, $result_value );
			
			if( $result_value == 0 )
			{
				return $thumbname;	
			}
			else
			{
				throw new Kea_Domain_Exception(
					'Something went wrong with thumbnail creation.  Ensure that the thumbnail directories have appropriate write permissions.'
				);
			}
		}
	}

// End class
}

?>

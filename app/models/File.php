<?php

class File extends Kea_Domain_Model
{
	public $file_id;
	public $file_title;
	public $file_publisher;
	public $file_language;
	public $file_relation;
	public $file_coverage;
	public $file_rights;
	public $file_source;
	public $file_subject;
	public $file_description;
	public $file_date;
	public $file_creator;
	public $file_additional_creator;
	public $object_id;
	//public $contributor_id;
	
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
	public $file_fullsize_filename;
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
		if ( $file->file_fullsize_filename )
		{
			$fullsize_path = ABS_FULLSIZE_DIR . DIRECTORY_SEPARATOR . $file->file_fullsize_filename;
			if( file_exists( $fullsize_path ) )
			{
				unlink( $fullsize_path );
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
		if ( $file->file_fullsize_filename )
		{
			$fullsize_path = ABS_FULLSIZE_DIR . DIRECTORY_SEPARATOR . $file->file_fullsize_filename;
			if( file_exists( $fullsize_path ) )
			{
				unlink( $fullsize_path );
			}
		}		
	}
	
	public static function add( $obj_id = null, $contributor_id = null, $file_form_name = null, $file_info_form = null )
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
												'file_description'			=> $file_info_form['file_description'],
												'file_size'					=> $_FILES[$file_form_name]['size'][$key],
												'file_mime_browser'			=> $_FILES[$file_form_name]['type'][$key],
												'file_authentication'		=> md5_file( $new_path ),
												'file_mime_php'				=> $mime_php,
												'file_mime_os'				=> $mime_os,
												'file_type_os'				=> $file_type,
												'file_archive_filename'		=> $new_name_string,
												'file_fullsize_filename'	=> self::createImage('fullsize', $new_path, null, FULLSIZE_IMAGE_SIZE ),
												'file_original_filename'	=> $name,
												'file_thumbnail_name'		=> self::createImage('thumbnail', $new_path, null, THUMBNAIL_SIZE ) );
				
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
	
	public static function createImage( $type, $file, $percent = null, $new_width = null, $new_height = null, $output = 3 )
	{
		if( empty($type) )
		{
			throw new Kea_Domain_Exception(
				'createImg must be passed a type of image to process'
			);
		}
		elseif( $type == 'thumbnail' )
		{
			$dir = ABS_THUMBNAIL_DIR;
		}
		elseif( $type == 'fullsize' )
		{
			$dir = ABS_FULLSIZE_DIR;
		}
		else
		{
			throw new Kea_Domain_Exception(
				'createImg was not passed a valid image type, no image created'
			);
			return false;
		}
		
		exec( PATH_TO_CONVERT . ' -version', $convert_version, $convert_return );
		if( $convert_return == 0 )
		{
			return self::imCreateImage($file, $dir, $percent, $new_width, $new_height );
		}
		elseif( function_exists( 'gd_info' ) )
		{
			return self::gdCreateImage( $file, $dir, $percent, $new_width, $new_height, $output );
		}
		else
		{
			throw new Kea_Domain_Exception(
				'Neither the GD Library nor Imagemagick is available to process thumbnails.'
			);
		}
	}
	
	public static function gdCreateImage($file, $dir, $percent = null, $new_width = null, $new_height = null, $output = 3)
	{
		try{
			set_time_limit(360);
			
			if ( !is_dir($dir) || !is_readable($dir) )
			{
				throw new Kea_Domain_Exception(
					'Image cannot be written to the directory ' . $dir . '.'
				);
				return false;
			}
			if( !function_exists( 'gd_info' ) ) {
				throw new Kea_Domain_Exception(
					'The GD Library is not installed and is required for this method.'
				);
				return false;
			}
			if( !function_exists( 'imagecreatefromjpeg') )
			{
				throw new Kea_Domain_Exception(
					'JPEG support is not installed for GD library.'
				);
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

				$img = imagecreatetruecolor( $new_width, $new_height );
			
				// From php.net, with a few bug fixes and a MAX_MEMORY setting
				// This function should probably go somewhere else

				self::setMemoryForImage($file);
			
				switch( $type ) {
					//GIF
					case( '1' ):
						$original = @imagecreatefromgif( $file );
					break;
					//JPG
					case( '2' ):
						$original = @imagecreatefromjpeg( $file );
					break;
					//PNG
					case( '3' ):
						$original = @imagecreatefrompng( $file );
					break;
					default:
						return false;
					break;
				}
			
				if(!$original)
				{
					throw new Kea_Domain_Exception( "Could not open file: ".basename($file) );
				}

				imagecopyresampled( $img, $original, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

				$filename = basename( $file );
				$new_name = explode( '.', $filename );
				$new_name[0] .= '_' . basename($dir);
				$imgname = implode( '.', $new_name );
			
				$new_path = rtrim( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $imgname;

				switch( $output ) {
					//GIF
					case( '1' ):
						imagegif( $img, $new_path );
					break;
					//JPG
					case( '2' ):
						imagejpeg( $img, $new_path, 100 );
					break;
					//PNG
					case( '3' ):
						imagepng( $img, $new_path );
					break;
					default:
						throw new Kea_Domain_Exception(
							'Can only encode thumbnails as png, jpeg, or gif.'
						);
					break;
				}
				imagedestroy( $original );
				imagedestroy( $img );
				return $imgname;
			}
			return false;
		} catch(Kea_Exception $e)
		{
			die( $e->__toString() );
		}
	}
	


	public static function imCreateImage( $file, $dir, $percent = null, $new_width = null, $new_height = null, $no_enlarge = TRUE )
	{
		
		if( !is_dir($dir) )
		{
			throw new Kea_Domain_Exception ('Invalid directory to put new image');
		}
		if( !is_writeable($dir) )
		{
			throw new Kea_Domain_Exception ('Unable to write to '. $dir . ' directory; improper permissions');
		}
		
		if( file_exists( $file ) && is_readable( $file ) && getimagesize( $file ) )
		{	
			list( $width, $height, $type ) = getimagesize( $file );
			
			$filename = basename( $file );
			$new_name = explode( '.', $filename );
			$new_name[0] .= '_' . basename($dir);
			$imagename = implode( '.', $new_name );
			$new_path = rtrim( $dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . $imagename;
			
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
			
			//We probably don't want to make images that are any bigger than the raw file
			if( $no_enlarge )
			{
				if ( ( !empty($new_width) && $new_width > $width ) || ( !empty($new_height) && $new_height > $height ) )
				{
					$command = PATH_TO_CONVERT . ' ' . $file . ' ' . $new_path;
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
	
	protected static function setMemoryForImage($filename)
	{
		// DEBUG
		//$debugEmail = '';
		
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
				return false;
			}
			
			ini_set('memory_limit', $newLimit . 'M');
			return true;
		} else {
			return false;
		}
	}
	
	public static function createArchiveFilename($oldFilename) {
		$newFilename = explode(".", basename($oldFilename) );
		$newFilename[0] .= '_' . substr( md5( mt_rand() + microtime( true ) ), 0, 10 );
		return implode(".", $newFilename);
	}
	
	public static function getIPTCvalues ( $filepath )
	{
		if ($size = getimagesize($filepath, $info)):
			if (isset($info["APP13"])):
				$iptc = iptcparse($info["APP13"]);
				$output['description'] = @$iptc["2#120"][0];
				$output['name'] = ucwords(strtolower(@$iptc["2#005"][0]));
				$output['creator'] = ucwords(strtolower(@$iptc["2#080"][0]));
				$output['creation_date'] = @$iptc["2#055"][0];
				$output['urgency'] = @$iptc["2#010"][0];
				$output['type'] = @$iptc["2#015"][0];
				$output['supp_types'] = @$iptc["2#020"][0];
				$output['spec_instr'] = @$iptc["2#040"][0];
				$output['credit_byline_title'] = @$iptc["2#085"][0];
				$output['city'] = @$iptc["2#090"][0];
				$output['state'] = @$iptc["2#095"][0];
				$output['country'] = @$iptc["2#101"][0];
				$output['location'] = 
					( ( !empty($output['city']) ) ? $output['city'] : NULL ) . 
					( ( !empty($output['state']) ) ? ', '.$output['state'] : NULL ) .
					( ( !empty($output['country']) ) ? ' '.$output['country'] : NULL);
				$output['country'] = @$iptc["2#101"][0];
				$output['otr'] = @$iptc["2#103"][0];
				$output['headline'] = @$iptc["2#105"][0];
				$output['source'] = @$iptc["2#110"][0];
				$output['photo_source'] = @$iptc["2#115"][0];
				return $output;
			else:
				return FALSE;
			endif;
		endif;
	}
	
	public function getShortDesc ( $length = 250 , $append = '...')
	{
		if (strlen($this->file_description) > $length ):
			$shortDesc = substr($this->file_description, 0, strrpos($this->file_description, ' ', $length-strlen($this->file_description)));
			$shortDesc = $shortDesc.$append;
			return $shortDesc;
		else: 
			return $this->file_description;
		endif;
	}
	
// End class
}

?>

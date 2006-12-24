<?php

final class Kea_Base
{	
	/*
		6/26	- added [^a-zA-Z0-9_] instead of [^a-z0-9_] and removed strtolower, allowing uppercase files in templates
	*/
	public static function formatName( $name )
    {
		return preg_replace( '/[^a-zA-Z0-9_]/', '', trim( $name, '_' ) );
    }
	
	// Simplified version of the Zend loadFile method
	public static function loadFile( $filename, $dir, $load = false, $regex = '/^[^a-zA-Z0-9]/' )
	{
		$filename = trim( $filename, '/' );
		$dir = rtrim( $dir, '/' );
		
		if ( preg_match( $regex, $filename ) ) {
			throw new Kea_Exception( 'File: ' . $filename . ' has an invalid character.' );
        }
		
		if( !is_dir( $dir ) || !is_readable( $dir ) ) {
			throw new Kea_Exception( 'Directory: ' . $dir . ' is invalid.' );
		}
		
		$filepath = $dir . DIRECTORY_SEPARATOR . $filename;

		if( file_exists( $filepath ) && is_readable( $filepath ) ) {
			if( $load ) require_once( $filepath );
			return $filepath;
		}
		return false;
	}

}

?>
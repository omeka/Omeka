<?php
/**
 * Environmental settings
 *
*/


/**
 *	Set the class autoload env
 */
function __autoload( $classname ) {
	$path = str_replace( '_', DIRECTORY_SEPARATOR, $classname );
	require_once( "$path.php" );
}

/**
 *	Handle uncaught exceptions by redirecting to a 404 page
 */
function uncaught_exception_handler( $e ) {
	ob_end_clean();
	include( ABS_CONTENT_DIR.DS.'404.php');
	exit();
}

date_default_timezone_set( 'America/New_York' );

/**
 *	Sets the function for top level uncaught exceptions
 */
set_exception_handler("uncaught_exception_handler");
?>
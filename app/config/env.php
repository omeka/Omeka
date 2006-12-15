<?php
/**
 * Environmental settings
 *
*/


/**
 *	Set the class autoload env
 */
/*
function __autoload( $classname ) {
	$path = str_replace( '_', DIRECTORY_SEPARATOR, $classname );
	require_once( "$path.php" );
}
*/

/**
 *	Handle uncaught exceptions by redirecting to a 404 page
 */
function uncaught_exception_handler( $e ) {
	$out = ob_get_contents();
	echo $out . $e->__toString();
	/**
	 * We can't include this call to the 404 page because that page runs 
	 * application code, and if there is an application failure, it var_dumps the 
	 * uncaught exception rather than a nice clean error message - [KBK]
	 */
	//include( ABS_CONTENT_DIR.DS.'404.php');
	exit();
}

date_default_timezone_set('America/New_York');

/**
 *	Sets the function for top level uncaught exceptions
 */
set_exception_handler("uncaught_exception_handler");
?>
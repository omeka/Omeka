<?php
set_include_path( get_include_path() . ':./app/lib' . ':./app/models' . ':./app/filters' );
define( 'DS', DIRECTORY_SEPARATOR );

/**
	DB connection
*/

define( 'KEA_MYSQLI_HOST',		'mysql.localdomain' );
define( 'KEA_MYSQLI_USERNAME',	'nagrin' );
define( 'KEA_MYSQLI_PASSWORD',	'dudeman' );
define( 'KEA_MYSQLI_DBNAME',	'sitebuilder_v01' );

define( 'KEA_MYSQLI_PORT',		null );
define( 'KEA_MYSQLI_SOCKET',	null );

/**
	Site specific details
*/
//I started embedding SITE_TITLE into template pagese, then Josh changed it to INFO_TITLE [KBK]

define( 'INFO_TITLE',			'Sitebuilder' );
define( 'SITE_TITLE',			INFO_TITLE);
define( 'THUMBNAIL_SIZE', 150);
define( 'EMAIL', 'DO_NOT_REPLY@siteurl.com');
define( 'FULLSIZE_IMAGE_SIZE', 600);
/*
	Google Maps key
*/
//define( 'GMAPS_KEY',			'ABQIAAAAD-SKaHlA87rO8jrVjT7SHBQ22YnqeXddIs-jHkCCm8C4K5z8GBTo29raXitwn3YbLGstzhF1Yn4Ctg' );
define( 'GMAPS_KEY',			'ABQIAAAAhxrOVZoPjvFB090WXAmN1hQ9iUszHZcPL8-0fTCLlz8-XEDUHhQ4FiFVo8WO6-J8nMxNiJOqnbZQUg');
/**
	Dir names
*/
define( 'APP_DIR_NAME',			'app' );
define( 'CONTENT_DIR_NAME',		'content' );
define( 'CONTROLLER_DIR_NAME',	'controllers' );
define( 'VAULT_DIR_NAME',		'vault' );
define( 'THUMBNAIL_DIR_NAME',	'thumbnails' );
define( 'LOG_DIR_NAME',			'logs' );
define( 'DROPBOX_DIR_NAME',        'dropbox' );
define( 'FULLSIZE_DIR_NAME',	'fullsize' );

/*
	Absolute directory locations
*/
define( 'ABS_APP_DIR',			ABS_ROOT.DS.APP_DIR_NAME );
define( 'ABS_CONTROLLER_DIR',	ABS_APP_DIR.DS.CONTROLLER_DIR_NAME );
define( 'ABS_CONTENT_DIR',		ABS_ROOT.DS.CONTENT_DIR_NAME );
define( 'ABS_VAULT_DIR',		ABS_CONTENT_DIR.DS.VAULT_DIR_NAME );
define( 'ABS_THUMBNAIL_DIR',	ABS_CONTENT_DIR.DS.THUMBNAIL_DIR_NAME );
define( 'ABS_DROPBOX_DIR',      ABS_CONTENT_DIR.DS.DROPBOX_DIR_NAME );
define( 'ABS_FULLSIZE_DIR',		ABS_CONTENT_DIR.DS.FULLSIZE_DIR_NAME );

/*
	Google Maps key
*/
define( 'GMAPS_KEY',			'ABQIAAAAD-SKaHlA87rO8jrVjT7SHBSEOfja84tcPloLMbnK5ptAw5ZCLxSaU2Xs7_Cpf3i8jTh4vsJ3LIzjZQ' );

/*
	Path to imagemagick's 'convert' function
*/
define( 'PATH_TO_CONVERT', '/usr/local/bin/convert' );

/*
	Global 404 file for the application
*/
define( 'GLOBAL_404',			ABS_CONTENT_DIR.DS.'404.php' );

/**
	Logging
*/
define( 'ABS_LOG_DIR',			ABS_APP_DIR.DS.LOG_DIR_NAME );
define( 'KEA_SQL_LOG',			ABS_LOG_DIR.DS.'sql.log' );
define( 'KEA_ERRORS_LOG',		ABS_LOG_DIR.DS.'errors.log' );
define( 'KEA_LOGINS_LOG',		ABS_LOG_DIR.DS.'logins.log' );
define( 'KEA_LOG_SQL',			false );
define( 'KEA_LOG_ERRORS',		false );
define( 'KEA_LOG_LOGINS',		false );
define( 'KEA_EMAIL_ERRORS', 	true );

/*
	Web specific settings for use in linking in files, link generation, etc.
*/

define( 'WEB_ROOT',				chop(dirname( $_SERVER['PHP_SELF'] ), '/') );
define( 'WEB_CONTENT_DIR',		WEB_ROOT.DS.CONTENT_DIR_NAME );
define( 'WEB_VAULT_DIR', 		WEB_CONTENT_DIR.DS.VAULT_DIR_NAME );
define( 'WEB_THUMBNAIL_DIR',	WEB_CONTENT_DIR.DS.THUMBNAIL_DIR_NAME );
define( 'SITE_BASE_URL', 'http://'. $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'] , 0, strrpos($_SERVER['PHP_SELF'], '/')) );
define( 'WEB_FULLSIZE_DIR', 	WEB_CONTENT_DIR.DS.FULLSIZE_DIR_NAME );

/*
	Theme directives, these will become dynamic
*/
define( 'PUBLIC_THEME_DIR',		DS.'public' );
define( 'ADMIN_THEME_DIR',		DS.'admin' );

/*
	Router settings
*/
define( 'ADMIN_URI', 'admin' );	// The uri which designates the route to the admin interface

/*
	Debug levels
*/
define( 'KEA_DEBUG_ERRORS',		1 );
define( 'KEA_DEBUG_TIMER',		false );
define( 'KEA_DEBUG_SQL',		false );
define( 'KEA_DEBUG_TEMPLATE',	true );

switch( KEA_DEBUG_ERRORS ) {
	case( 1 ): error_reporting( E_ALL | E_STRICT );		break;
	case( 2 ): error_reporting( E_ALL );				break;
	case( 3 ): error_reporting( E_ALL  ^  E_NOTICE );	break;
	case( 4 ): error_reporting( E_WARNING );			break;
	case( 5 ): error_reporting( 0 );					break;
}

/**
 * These should be loaded in a specific order
 * 'env' -> 'stdlib' -> 'routes'
 */
require_once('env.php');
require_once('stdlib.php');
require_once('routes.php');

?>

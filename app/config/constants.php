<?php
/**
 * Kea application name
 */
define('APP_NAME', 'SITEBUILDER codename KIWI');

/**
 * Dirs
 */
define('KEA_CONTROLLER_DIR',	KEA_ROOT.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'controllers');
define('KEA_THEME_DIR',			KEA_ROOT.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'themes');
define('KEA_JSON_DIR',			KEA_ROOT.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'templates');
define('KEA_REST_DIR',			KEA_ROOT.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'rest'.DIRECTORY_SEPARATOR.'templates');

/**
 * Controller Settings
 */
define('DEFAULT_CONTROLLER', 'index');
define('DEFAULT_ACTION', 'index');

// This is temporary for testing and should become dynamic
define('KEA_THEME', 'n8agrin');





/**
	DB connection
*/

define( 'KEA_MYSQLI_HOST',		'localhost' );
define( 'KEA_MYSQLI_USERNAME',	'root' );
define( 'KEA_MYSQLI_PASSWORD',	'' );
define( 'KEA_MYSQLI_DBNAME',	'sb-v1' );

define( 'KEA_MYSQLI_PORT',		null );
define( 'KEA_MYSQLI_SOCKET',	null );
define( 'KEA_LOG_SQL',			false );
/**
	Site specific details
*/
//I started embedding SITE_TITLE into template pages, then Josh changed it to INFO_TITLE [KBK]
/*
define( 'INFO_TITLE',			'Sitebuilder' );
define( 'SITE_TITLE',			INFO_TITLE);
define( 'THUMBNAIL_SIZE', 250);
define( 'EMAIL', 'DO_NOT_REPLY@siteurl.com');
*/

/**
	Dir names
*/
/*
define( 'APP_DIR_NAME',			'app' );
define( 'CONTENT_DIR_NAME',		'content' );
define( 'CONTROLLER_DIR_NAME',	'controllers' );
define( 'VAULT_DIR_NAME',		'vault' );
define( 'THUMBNAIL_DIR_NAME',	'thumbnails' );
define( 'LOG_DIR_NAME',			'logs' );
define( 'DROPBOX_DIR_NAME',		'dropbox' );
*/
/*
	Absolute directory locations
*/
/*
define( 'ABS_APP_DIR',			ABS_ROOT.DS.APP_DIR_NAME );
define( 'ABS_CONTROLLER_DIR',	ABS_APP_DIR.DS.CONTROLLER_DIR_NAME );
define( 'ABS_CONTENT_DIR',		ABS_ROOT.DS.CONTENT_DIR_NAME );
define( 'ABS_VAULT_DIR',		ABS_CONTENT_DIR.DS.VAULT_DIR_NAME );
define( 'ABS_THUMBNAIL_DIR',	ABS_CONTENT_DIR.DS.THUMBNAIL_DIR_NAME );
define( 'ABS_DROPBOX_DIR',      ABS_CONTENT_DIR.DS.DROPBOX_DIR_NAME );
*/
/*
	Google Maps key
*/
/*
define( 'GMAPS_KEY',			'ABQIAAAAD-SKaHlA87rO8jrVjT7SHBSEOfja84tcPloLMbnK5ptAw5ZCLxSaU2Xs7_Cpf3i8jTh4vsJ3LIzjZQ' );
*/
/*
	Path to imagemagick's 'convert' function
*/
/*
define( 'PATH_TO_CONVERT', '/usr/local/bin/convert' );
*/
/*
	Global 404 file for the application
*/
/*
define( 'GLOBAL_404',			ABS_CONTENT_DIR.DS.'404.php' );
*/
/**
	Logging
*/
/*
define( 'ABS_LOG_DIR',			ABS_APP_DIR.DS.LOG_DIR_NAME );
define( 'KEA_SQL_LOG',			ABS_LOG_DIR.DS.'sql.log' );
define( 'KEA_ERRORS_LOG',		ABS_LOG_DIR.DS.'errors.log' );
define( 'KEA_LOGINS_LOG',		ABS_LOG_DIR.DS.'logins.log' );

define( 'KEA_LOG_ERRORS',		false );
define( 'KEA_LOG_LOGINS',		false );
define( 'KEA_EMAIL_ERRORS', 	true );
*/
/*
	Web specific settings for use in linking in files, link generation, etc.
*/
/*
define( 'WEB_ROOT',				chop(dirname( $_SERVER['PHP_SELF'] ), '/') );
define( 'WEB_CONTENT_DIR',		WEB_ROOT.DS.CONTENT_DIR_NAME );
define( 'WEB_VAULT_DIR', 		WEB_CONTENT_DIR.DS.VAULT_DIR_NAME );
define( 'WEB_THUMBNAIL_DIR',	WEB_CONTENT_DIR.DS.THUMBNAIL_DIR_NAME );
define( 'SITE_BASE_URL', 'http://'. $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'] , 0, strrpos($_SERVER['PHP_SELF'], '/')) );
*/
/*
	Theme directives, these will become dynamic
*/
/*
define( 'PUBLIC_THEME_DIR',		DS.'public' );
define( 'ADMIN_THEME_DIR',		DS.'admin' );
*/
/*
	Router settings
*/
/*
define( 'ADMIN_URI', 'admin' );	// The uri which designates the route to the admin interface
*/


?>
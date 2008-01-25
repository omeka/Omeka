<?php
define('OMEKA_MIGRATION', 17);
define('OMEKA_VERSION', '1.0rc4');

error_reporting(E_ALL ^ E_NOTICE);

// Define some primitive settings
// if we can reuse these that might not be a bad thing
$site['application']	= 'application';
$site['core']			= 'core';
$site['libraries']		= 'libraries';
$site['helpers']		= 'helpers';
$site['controllers']	= 'controllers';
$site['models']			= 'models';
$site['config']			= 'config';
$site['admin']			= 'admin';
$site['shared']			= 'shared';
$site['plugins']		= 'plugins';
$site['logs']			= 'logs';
$site['archive']		= 'archive';
$site['fullsize']		= 'fullsize';
$site['thumbnails']		= 'thumbnails';
$site['square_thumbnails'] = 'square_thumbnails';
$site['files']			= 'files';
$site['exhibit_layouts']= 'exhibit_layouts';
$site['exhibit_themes'] = 'exhibit_themes';
$site['public_theme']	= 'themes';

// Define some constants based on those settings
define('BASE_DIR', 			dirname(__FILE__));
define('APP_DIR',			BASE_DIR.DIRECTORY_SEPARATOR.$site['application']);
define('CORE_DIR',			APP_DIR.DIRECTORY_SEPARATOR.$site['core']);
define('MODEL_DIR',			APP_DIR.DIRECTORY_SEPARATOR.$site['models']);
define('CONTROLLER_DIR',	APP_DIR.DIRECTORY_SEPARATOR.$site['controllers']);
define('LIB_DIR',			APP_DIR.DIRECTORY_SEPARATOR.$site['libraries']);
define('HELPER_DIR',		APP_DIR.DIRECTORY_SEPARATOR.$site['helpers']);
define('CONFIG_DIR',		APP_DIR.DIRECTORY_SEPARATOR.$site['config']);
define('PLUGIN_DIR',		BASE_DIR.DIRECTORY_SEPARATOR.$site['plugins']);
define('SHARED_DIR', 		BASE_DIR.DIRECTORY_SEPARATOR.$site['shared']);
define('ADMIN_DIR', 		BASE_DIR.DIRECTORY_SEPARATOR.$site['admin']);
define('LOGS_DIR',			APP_DIR.DIRECTORY_SEPARATOR.$site['logs']);
define('ARCHIVE_DIR', 		BASE_DIR.DIRECTORY_SEPARATOR.$site['archive']);
define('THUMBNAIL_DIR', 	ARCHIVE_DIR.DIRECTORY_SEPARATOR.$site['thumbnails']);
define('SQUARE_THUMBNAIL_DIR', 	ARCHIVE_DIR.DIRECTORY_SEPARATOR.$site['square_thumbnails']);
define('FULLSIZE_DIR', 		ARCHIVE_DIR.DIRECTORY_SEPARATOR.$site['fullsize']);
define('FILES_DIR', 		ARCHIVE_DIR.DIRECTORY_SEPARATOR.$site['files']);
define('EXHIBIT_LAYOUTS_DIR',		SHARED_DIR.DIRECTORY_SEPARATOR.$site['exhibit_layouts']);
define('EXHIBIT_THEMES_DIR',SHARED_DIR.DIRECTORY_SEPARATOR.$site['exhibit_themes']);

//Define the main file that will load all the helpers
define('HELPERS',			HELPER_DIR.DIRECTORY_SEPARATOR.'all.php');

// Abs theme dir is set in the appropriate index.php file

// Define Web routes
$root = 'http://'.$_SERVER['HTTP_HOST'];
//This looks ugly but plugin and shared directories are at the root of the site, whereas WEB_THEME is either root or root/admin depending - KK
define('WEB_ROOT', 		$root.dirname(str_replace('/admin', '', $_SERVER['PHP_SELF'])));
define('WEB_DIR',		$root.dirname($_SERVER['PHP_SELF']));
define('WEB_THEME',		WEB_DIR.DIRECTORY_SEPARATOR.'themes');
define('WEB_SHARED',	WEB_ROOT.DIRECTORY_SEPARATOR.$site['shared']);
define('WEB_PLUGIN',	WEB_ROOT.DIRECTORY_SEPARATOR.$site['plugins']);
define('WEB_ARCHIVE',	WEB_ROOT.DIRECTORY_SEPARATOR.$site['archive']);
define('WEB_THUMBNAILS',WEB_ARCHIVE.DIRECTORY_SEPARATOR.$site['thumbnails']);
define('WEB_SQUARE_THUMBNAILS',  WEB_ARCHIVE.DIRECTORY_SEPARATOR.$site['square_thumbnails']);
define('WEB_FULLSIZE',	WEB_ARCHIVE.DIRECTORY_SEPARATOR.$site['fullsize']);
define('WEB_FILES',		WEB_ARCHIVE.DIRECTORY_SEPARATOR.$site['files']);
define('WEB_EXHIBIT_LAYOUTS', WEB_SHARED.DIRECTORY_SEPARATOR.$site['exhibit_layouts'] );
define('WEB_EXHIBIT_THEMES',  WEB_SHARED.DIRECTORY_SEPARATOR.$site['exhibit_themes']);
define('WEB_PUBLIC_THEME',	WEB_ROOT.DIRECTORY_SEPARATOR.$site['public_theme']);
// Set the include path to the library path
// do we want to include the model paths here too? [NA]
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries'].PATH_SEPARATOR.MODEL_DIR);
?>

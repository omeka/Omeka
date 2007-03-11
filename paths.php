<?php
// Define some primitive settings
// if we can reuse these that might not be a bad thing
$site['application']	= 'application';
$site['libraries']		= 'libraries';
$site['controllers']	= 'controllers';
$site['models']			= 'models';
$site['config']			= 'config';
$site['admin']			= 'admin';
$site['shared']			= 'shared';
$site['plugins']		= 'plugins';

// Define some constants based on those settings
define('BASE_DIR', 			dirname(__FILE__));
define('APP_DIR',			BASE_DIR.DIRECTORY_SEPARATOR.$site['application']);
define('MODEL_DIR',			APP_DIR.DIRECTORY_SEPARATOR.$site['models']);
define('CONTROLLER_DIR',	APP_DIR.DIRECTORY_SEPARATOR.$site['controllers']);
define('LIB_DIR',			APP_DIR.DIRECTORY_SEPARATOR.$site['libraries']);
define('CONFIG_DIR',		APP_DIR.DIRECTORY_SEPARATOR.$site['config']);
define('PLUGIN_DIR',		BASE_DIR.DIRECTORY_SEPARATOR.$site['plugins']);
define('SHARED_DIR', 		BASE_DIR.DIRECTORY_SEPARATOR.$site['shared']);
define('ADMIN_DIR', 		BASE_DIR.DIRECTORY_SEPARATOR.$site['admin']);
// Abs theme dir is set in the appropriate index.php file

// Define Web routes
$root = 'http://'.$_SERVER['HTTP_HOST'];
define('WEB_DIR',		$root.dirname($_SERVER['PHP_SELF']));
define('WEB_THEME',		WEB_DIR.DIRECTORY_SEPARATOR.'themes');
define('WEB_SHARED',	WEB_DIR.DIRECTORY_SEPARATOR.'shared');

// Set the include path to the library path
// do we want to include the model paths here too? [NA]
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries'].DIRECTORY_SEPARATOR.'Zend_Incubator'.PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries']);
?>
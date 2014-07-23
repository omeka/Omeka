<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package Omeka
 */

// Define the current version of Omeka.
define('OMEKA_VERSION', '2.2.2');

// Define the application environment.
if (!defined('APPLICATION_ENV')) {
    if (!($app_env = getenv('APPLICATION_ENV'))) {
        if (!($app_env = getenv('REDIRECT_APPLICATION_ENV'))) {
            $app_env = 'production';
        }
    }
    define('APPLICATION_ENV', $app_env);
}

// Define directory path constants.

define('BASE_DIR', dirname(__FILE__));
define('APP_DIR', BASE_DIR . '/application');
define('PLUGIN_DIR', BASE_DIR . '/plugins');
define('ADMIN_DIR', BASE_DIR . '/admin');
define('FILES_DIR', BASE_DIR . '/files');
define('ADMIN_THEME_DIR', BASE_DIR . '/admin/themes');
define('PUBLIC_THEME_DIR', BASE_DIR . '/themes');
define('INSTALL_DIR', BASE_DIR . '/install');
define('MODEL_DIR', APP_DIR . '/models');
define('FORM_DIR', APP_DIR . '/forms');
define('CONTROLLER_DIR', APP_DIR . '/controllers');
define('LIB_DIR', APP_DIR . '/libraries');
define('CONFIG_DIR', APP_DIR . '/config');
define('LOGS_DIR', APP_DIR . '/logs');
define('VIEW_SCRIPTS_DIR', APP_DIR . '/views/scripts');
define('VIEW_HELPERS_DIR', APP_DIR . '/views/helpers');
define('UPGRADE_DIR', APP_DIR . '/migrations');
define('LANGUAGES_DIR', APP_DIR . '/languages');
define('SCHEMA_DIR', APP_DIR . '/schema');
define('SCRIPTS_DIR', APP_DIR . '/scripts');

// Define the web address constants.

// Set the scheme.
$base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';

// Set the domain.
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = null;
}
$base_url = $base_root .= '://' . preg_replace('/[^a-z0-9-:._]/i', '', $_SERVER['HTTP_HOST']);

// Set to port, if any.
if (!isset($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = null;
}
$port = $_SERVER['SERVER_PORT'];
if (($base_root == 'http' && $port != '80') || ($base_root == 'https' && $port != '443')) {
    $base_url .= ":$port";
}

// Set the path.
if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
    $base_path  = "/$dir";
    $base_url  .= $base_path;
    $base_path .= '/';
} else {
    $base_path = '/';
}

// Remove the '/admin' part of the URL by regex, if necessary.
if (defined('ADMIN')) {
    $dir = preg_replace('/(.*)admin$/', '$1', $dir, 1);
    $dir = rtrim($dir, '/');
}

// WEB_ROOT is always the root of the site, whereas WEB_DIR depends on the 
// bootstrap used (public/admin)
define('WEB_ROOT', $base_root . (!empty($dir) ? '/' . $dir : '') );
define('WEB_DIR', $base_url);
define('WEB_THEME', WEB_DIR . '/themes');
define('WEB_PLUGIN', WEB_ROOT . '/plugins');
define('WEB_FILES', WEB_ROOT . '/files');
define('WEB_PUBLIC_THEME', WEB_ROOT . '/themes');
define('WEB_VIEW_SCRIPTS', WEB_ROOT . '/application/views/scripts');

// Get the directory that the bootstrap sits in.
$dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/');

// current path should be empty and not a '/' if there is no directory path.
$currentPath = !empty($dir) ? "/$dir" : '';
define('ADMIN_WEB_DIR', 'admin');

// This is how we determine whether or not we are in the admin bootstrap.
if (defined('ADMIN')) {
    $adminPath = $currentPath;
    // Strip off the admin directory to get the public dir.
    $publicPath = rtrim(preg_replace("/(.*)" . ADMIN_WEB_DIR . "$/", '$1', $currentPath, 1), '/');
} else {
    $adminPath = "$currentPath/" . ADMIN_WEB_DIR;
    $publicPath = $currentPath;
}

if (defined('INSTALL')) {
    $publicPath = substr($publicPath, 0,  strlen($publicPath) - strlen('/install'));
    $adminPath = "$publicPath/" . ADMIN_WEB_DIR;
}

define('INSTALL_WEB_DIR', 'install');
$installPath = "$publicPath/" . INSTALL_WEB_DIR;

define('ADMIN_BASE_URL', $adminPath);
define('PUBLIC_BASE_URL', $publicPath);
define('INSTALL_BASE_URL', $installPath);
define('CURRENT_BASE_URL', $currentPath);

// If date.timezone is not set, this will query the OS for the timezone and set 
// that as the default. Workaround for PHP 5.3 behavior for timezones.
date_default_timezone_set(@date_default_timezone_get());

// Set the zlib config values if the extension has been loaded.
if (extension_loaded('zlib')) {
    ini_set('zlib.output_compression', true);
    ini_set('zlib.output_compression_level', '5');
}

// Strip slashes from superglobals to avoid problems with PHP's magic_quotes.
if (get_magic_quotes_gpc()) {
    $_GET = stripslashes_deep($_GET);
    $_POST = stripslashes_deep($_POST);
    $_COOKIE = stripslashes_deep($_COOKIE);
    $_REQUEST = stripslashes_deep($_REQUEST);
}

// Add the libraries and models directories to the include path.
set_include_path(LIB_DIR. PATH_SEPARATOR . MODEL_DIR . PATH_SEPARATOR . get_include_path());

// Set up the Zend_Loader autoloader to work for all classes. The Omeka 
// namespace must be manually specified to avoid incompatibility with the
// resource autoloader.
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Omeka_');
$autoloader->setFallbackAutoloader(true);
$autoloader->suppressNotFoundWarnings(true);

// Define the theme directory path.
define('THEME_DIR', defined('ADMIN') ? ADMIN_THEME_DIR : PUBLIC_THEME_DIR);

/**
 * Strip slashes recursively.
 *
 * @param array|string $value
 * @return array
 */
function stripslashes_deep($value)
{
    return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
}

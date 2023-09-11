<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 * @package Omeka
 */

// Define the current version of Omeka.
define('OMEKA_VERSION', '3.1.2');

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
if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] === true))
    || (isset($_SERVER['HTTP_SCHEME']) && $_SERVER['HTTP_SCHEME'] == 'https')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
) {
    $scheme = 'https';
} else {
    $scheme = 'http';
}

// Set the domain and port.
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = null;
}
$absoluteBase = $scheme . '://' . preg_replace('/[^a-z0-9-:._]/i', '', (string) $_SERVER['HTTP_HOST']);

// Set the path.
$dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/');
// current path should be empty and not a '/' if there is no directory path.
$currentPath = $dir ? "/$dir" : '';

define('ADMIN_WEB_DIR', 'admin');
define('INSTALL_WEB_DIR', 'install');

// Remove the '/admin' part of the URL by regex, if necessary.
if (defined('ADMIN')) {
    $dir = preg_replace('/(.*)admin$/', '$1', $dir, 1);
    $dir = rtrim($dir, '/');

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

$installPath = "$publicPath/" . INSTALL_WEB_DIR;

$rootPath = $dir ? "/$dir" : '';

define('WEB_RELATIVE_THEME', $currentPath . '/themes');
define('WEB_RELATIVE_PLUGIN', $rootPath . '/plugins');
define('WEB_RELATIVE_FILES', $rootPath . '/files');
define('WEB_RELATIVE_PUBLIC_THEME', $rootPath . '/themes');
define('WEB_RELATIVE_VIEW_SCRIPTS', $rootPath . '/application/views/scripts');

// WEB_ROOT is always the root of the site, whereas WEB_DIR depends on the 
// bootstrap used (public/admin)
define('WEB_ROOT', $absoluteBase . $rootPath);
define('WEB_DIR', $absoluteBase . $currentPath);
define('WEB_THEME', WEB_DIR . '/themes');
define('WEB_PLUGIN', WEB_ROOT . '/plugins');
define('WEB_FILES', WEB_ROOT . '/files');
define('WEB_PUBLIC_THEME', WEB_ROOT . '/themes');
define('WEB_VIEW_SCRIPTS', WEB_ROOT . '/application/views/scripts');

define('ADMIN_BASE_URL', $adminPath);
define('PUBLIC_BASE_URL', $publicPath);
define('INSTALL_BASE_URL', $installPath);
define('CURRENT_BASE_URL', $currentPath);

// If date.timezone is not set, this will query the OS for the timezone and set 
// that as the default. Workaround for PHP 5.3 behavior for timezones.
date_default_timezone_set(@date_default_timezone_get());

// Set the zlib config values if the extension has been loaded.
if (PHP_SAPI !== 'cli' && extension_loaded('zlib')) {
    ini_set('zlib.output_compression', true);
    ini_set('zlib.output_compression_level', '5');
}

// Add the libraries and models directories to the include path.
set_include_path(LIB_DIR. PATH_SEPARATOR . MODEL_DIR . PATH_SEPARATOR . get_include_path());

// Set up the Zend autoloader to work for all classes.
require_once 'Zend/Loader/StandardAutoloader.php';
$autoloader = new Zend_Loader_StandardAutoloader(array(
    'prefixes' => array(
        'Omeka_Form_' => APP_DIR . '/forms',
        'Omeka_View_Helper_' => APP_DIR . '/views/helpers',
        'Omeka_Controller_Action_Helper' => APP_DIR . '/controllers/helpers',
    ),
    'fallback_autoloader' => true,
));
$autoloader->register();

// Define the theme directory path.
define('THEME_DIR', defined('ADMIN') ? ADMIN_THEME_DIR : PUBLIC_THEME_DIR);

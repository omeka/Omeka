<?php
/**
 * Constants for paths and other global metadata.
 * 
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Define the current version of Omeka.
 */ 
define('OMEKA_VERSION', '2.0-dev');

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// The name of the Item Type Metadata element set. This is used wherever it is 
// important to distinguish this particular element set from other element sets.
// @todo Localize this and other constants to avoid too many things in the 
// global scope.
define('ELEMENT_SET_ITEM_TYPE', 'Item Type Metadata');

// Workaround for PHP 5.3 behavior for timezones.
// If date.timezone is not set, this will query the OS for the timezone
// and set that as the default.
date_default_timezone_set(@date_default_timezone_get());

// Set the zlib config values if the extension has been loaded.
if (extension_loaded('zlib')) {
    ini_set('zlib.output_compression', true);
    ini_set('zlib.output_compression_level', '5');    
}

// Define directory path constants:
define('BASE_DIR',         dirname(__FILE__));
define('APP_DIR',          BASE_DIR . '/application');
define('PLUGIN_DIR',       BASE_DIR . '/plugins');
define('ADMIN_DIR',        BASE_DIR . '/admin');
define('FILES_DIR',        BASE_DIR . '/files');
define('ADMIN_THEME_DIR',  BASE_DIR . '/admin/themes');
define('PUBLIC_THEME_DIR', BASE_DIR . '/themes');
define('INSTALL_DIR',      BASE_DIR . '/install');
define('MODEL_DIR',        APP_DIR . '/models');
define('FORM_DIR',         APP_DIR . '/forms');
define('CONTROLLER_DIR',   APP_DIR . '/controllers');
define('LIB_DIR',          APP_DIR . '/libraries');
define('CONFIG_DIR',       APP_DIR . '/config');
define('LOGS_DIR',         APP_DIR . '/logs');
define('VIEW_SCRIPTS_DIR', APP_DIR . '/views/scripts');
define('VIEW_HELPERS_DIR', APP_DIR . '/views/helpers');
define('UPGRADE_DIR',      APP_DIR . '/migrations');
define('LANGUAGES_DIR',    APP_DIR . '/languages');
define('SCHEMA_DIR',       APP_DIR . '/schema');
define('SCRIPTS_DIR',      APP_DIR . '/scripts');

define('BACKGROUND_BOOTSTRAP_PATH', SCRIPTS_DIR . '/background.php');

// Define the web address constants:
defined('WEB_ROOT') || _define_web_root();
define('WEB_THEME',        WEB_DIR . '/themes');
define('WEB_PLUGIN',       WEB_ROOT . '/plugins');
define('WEB_FILES',        WEB_ROOT . '/files');
define('WEB_PUBLIC_THEME', WEB_ROOT . '/themes');
define('WEB_VIEW_SCRIPTS', WEB_ROOT . '/application/views/scripts');

// Set the include path for the models directory.
set_include_path(LIB_DIR. PATH_SEPARATOR . MODEL_DIR . PATH_SEPARATOR . get_include_path());

/**
 * Most of this has been borrowed directly from Drupal 6.1's 
 * bootstrap code (bootstrap.inc, conf_init())
 *
 * @access private
 * @todo rename the WEB_ROOT, WEB_DIR constants and add a new one for the 
 * $base_path
 * @return void
 **/
function _define_web_root()
{
    // Create base URL
    $base_root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
    
    // As $_SERVER['HTTP_HOST'] is user input, ensure it only contains 
    // characters allowed in hostnames.
    $base_url = $base_root .= '://' . preg_replace('/[^a-z0-9-:._]/i', '', $_SERVER['HTTP_HOST']);

    // Handle non-standard ports
    $port = $_SERVER['SERVER_PORT'];
    if (($base_root == 'http' && $port != '80') || ($base_root == 'https' && $port != '443')) {
        $base_url .= ":$port";
    }
    
    // $_SERVER['SCRIPT_NAME'] can, in contrast to $_SERVER['PHP_SELF'], not
    // be modified by a visitor.
    if ($dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/')) {
        $base_path  = "/$dir";
        $base_url  .= $base_path;
        $base_path .= '/';
    } else {
        $base_path = '/';
    }
        
    // WEB_ROOT is always the root of the site, whereas WEB_DIR depends on the 
    // bootstrap used (public/admin)
    
    // @hack Remove the '/admin' part of the URL by regex (only if necessary)
    if (defined('ADMIN')) {
        $dir = preg_replace('/(.*)admin$/', '$1', $dir, 1);
        $dir = rtrim($dir, '/');
    }
   
   define('WEB_ROOT', $base_root . (!empty($dir) ? '/' . $dir : '') );
   define('WEB_DIR',  $base_url);  
}

// Get the directory that the bootstrap sits in.
$dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/');

// current path should be empty and not a '/' if there is no directory path.
$currentPath = !empty($dir) ? "/$dir" : '';
$adminDir = 'admin';

// This is how we determine whether or not we are in the admin bootstrap.
if (defined('ADMIN')) {
    $adminPath = $currentPath;
    // Strip off the admin directory to get the public dir.
    // @hack Remove the '/admin' part of the URL by regex (only if necessary).
    $publicPath = rtrim(preg_replace("/(.*)$adminDir$/", '$1', $currentPath, 1), '/');
} else {
    $adminPath = "$currentPath/$adminDir";
    $publicPath = $currentPath;
}

define('ADMIN_BASE_URL', $adminPath);
define('PUBLIC_BASE_URL', $publicPath);
define('CURRENT_BASE_URL', $currentPath);    

// Set up the Zend_Loader autoloader to work for all classes.
// The Omeka namespace must be manually specified to avoid incompatibility with the
// resource autoloader.
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Omeka_');
$autoloader->setFallbackAutoloader(true);
$autoloader->suppressNotFoundWarnings(true);

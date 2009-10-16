<?php
/**
 * Define constants for paths and other global metadata.
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

// Define the current migration.
define('OMEKA_MIGRATION', 42);

// Define the current version.
define('OMEKA_VERSION', '1.1');

// The name of the Item Type Metadata element set. This is used wherever it is 
// important to distinguish this particular element set from other element sets.
// @todo Localize this and other constants to avoid too many things in the 
// global scope.
define('ELEMENT_SET_ITEM_TYPE', 'Item Type Metadata');

// Report all errors except E_NOTICE.
error_reporting(E_ALL ^ E_NOTICE);

// Set the zlib config values if the extension has been loaded.
if (extension_loaded('zlib')) {
    ini_set('zlib.output_compression', true);
    ini_set('zlib.output_compression_level', '5');    
}

// Set directory names:
$site['application']       = 'application';
$site['core']              = 'core';
$site['libraries']         = 'libraries';
$site['helpers']           = 'helpers';
$site['controllers']       = 'controllers';
$site['models']            = 'models';
$site['config']            = 'config';
$site['admin']             = 'admin';
$site['plugins']           = 'plugins';
$site['logs']              = 'logs';
$site['archive']           = 'archive';
$site['fullsize']          = 'fullsize';
$site['thumbnails']        = 'thumbnails';
$site['square_thumbnails'] = 'square_thumbnails';
$site['files']             = 'files';
$site['public_theme']      = 'themes';
$site['admin_theme']       = 'themes';
$site['views']             = 'views';
$site['scripts']           = 'scripts';
$site['migrations']        = 'migrations';
$site['install']           = 'install';

// Define directory path constants:
define('BASE_DIR',         dirname(__FILE__));
define('APP_DIR',          BASE_DIR . DIRECTORY_SEPARATOR . $site['application']);
define('PLUGIN_DIR',       BASE_DIR . DIRECTORY_SEPARATOR . $site['plugins']);
define('ADMIN_DIR',        BASE_DIR . DIRECTORY_SEPARATOR . $site['admin']);
define('ARCHIVE_DIR',      BASE_DIR . DIRECTORY_SEPARATOR . $site['archive']);
define('ADMIN_THEME_DIR',  BASE_DIR . DIRECTORY_SEPARATOR . $site['admin'] . DIRECTORY_SEPARATOR . $site['admin_theme']);
define('PUBLIC_THEME_DIR', BASE_DIR . DIRECTORY_SEPARATOR . $site['public_theme']);
define('INSTALL_DIR',      BASE_DIR . DIRECTORY_SEPARATOR . $site['install']);
define('CORE_DIR',         APP_DIR . DIRECTORY_SEPARATOR . $site['core']);
define('MODEL_DIR',        APP_DIR . DIRECTORY_SEPARATOR . $site['models']);
define('CONTROLLER_DIR',   APP_DIR . DIRECTORY_SEPARATOR . $site['controllers']);
define('LIB_DIR',          APP_DIR . DIRECTORY_SEPARATOR . $site['libraries']);
define('HELPER_DIR',       APP_DIR . DIRECTORY_SEPARATOR . $site['helpers']);
define('CONFIG_DIR',       APP_DIR . DIRECTORY_SEPARATOR . $site['config']);
define('LOGS_DIR',         APP_DIR . DIRECTORY_SEPARATOR . $site['logs']);
define('VIEW_SCRIPTS_DIR', APP_DIR . DIRECTORY_SEPARATOR . $site['views'] . DIRECTORY_SEPARATOR . $site['scripts']);
define('UPGRADE_DIR',      APP_DIR . DIRECTORY_SEPARATOR . $site['migrations']);
define('THUMBNAIL_DIR',        ARCHIVE_DIR . DIRECTORY_SEPARATOR . $site['thumbnails']);
define('SQUARE_THUMBNAIL_DIR', ARCHIVE_DIR . DIRECTORY_SEPARATOR . $site['square_thumbnails']);
define('FULLSIZE_DIR',         ARCHIVE_DIR . DIRECTORY_SEPARATOR . $site['fullsize']);
define('FILES_DIR',            ARCHIVE_DIR . DIRECTORY_SEPARATOR . $site['files']);

define('BACKGROUND_BOOTSTRAP_PATH', CORE_DIR . DIRECTORY_SEPARATOR . 'background.php');

// Define the script that loads all the helpers:
define('HELPERS', HELPER_DIR . DIRECTORY_SEPARATOR . 'all.php');

// Define the web address constants:
defined('WEB_ROOT') || _define_web_root();
define('WEB_THEME', WEB_DIR . '/themes');
define('WEB_PLUGIN',       WEB_ROOT . '/' . $site['plugins']);
define('WEB_ARCHIVE',      WEB_ROOT . '/' . $site['archive']);
define('WEB_PUBLIC_THEME', WEB_ROOT . '/' . $site['public_theme']);
define('WEB_VIEW_SCRIPTS', WEB_ROOT . '/' . $site['application'] . '/' . $site['views'] . '/' . $site['scripts']);
define('WEB_THUMBNAILS',        WEB_ARCHIVE . '/' . $site['thumbnails']);
define('WEB_SQUARE_THUMBNAILS', WEB_ARCHIVE . '/' . $site['square_thumbnails']);
define('WEB_FULLSIZE',          WEB_ARCHIVE . '/' . $site['fullsize']);
define('WEB_FILES',             WEB_ARCHIVE . '/' . $site['files']);

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

// function _define_url_constants()
// {
    // Get the directory that the bootstrap sits in.
    $dir = trim(dirname($_SERVER['SCRIPT_NAME']), '\,/');
    // current path should be empty and not a '/' if there is no directory path.
    $currentPath = !empty($dir) ? "/$dir" : '';
    $adminDir = $site['admin'];
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
// }


// Unfortunately we can't use the Zend_Loader instead, because it
// throws warnings when it can't find a file. On the other hand,
// Omeka::autoload() never tries to include the file if it doesn't
// exist.
require_once 'Omeka.php';
spl_autoload_register(array('Omeka', 'autoload'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'production'));

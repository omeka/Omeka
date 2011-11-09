<?php
/**
 * Bootstrap for admin interface.  
 *
 * This is the same as the public interface bootstrap, except it defines an
 * ADMIN constant and sets an 'admin' parameter in the web request to ensure
 * that Omeka loads the correct view scripts (and any other theme-specific
 * behavior).  
 *
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

// Flag this as the admin theme. Used by _define_web_root() function in paths.php.
define('ADMIN', true);

// Define the directory and web paths.
include '../paths.php';

// Define the admin theme directory path.
define('THEME_DIR', ADMIN_DIR . '/' . $site['admin_theme']);

$app = new Omeka_Core;
// Configure the Theme bootstrap resource with the correct paths/URLs.
$app->getBootstrap()->setOptions(array(
    'resources' => array(
        'theme' => array(
            'basePath' => THEME_DIR,
            'webBasePath' => WEB_THEME
        )
    )
));

// This is used by the global is_admin_theme to detect that this is the admin.
Zend_Controller_Front::getInstance()->setParam('admin', true);

$app->initialize();

// This plugin allows for all functionality that is specific to the 
// admin theme.
$app->getBootstrap()->getResource('FrontController')->registerPlugin(new Omeka_Controller_Plugin_Admin);
$app->run();

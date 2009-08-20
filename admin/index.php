<?php
/**
 * Bootstrap for admin interface.  
 *
 * This is the same as the public interface bootstrap, except it defines an
 * ADMIN constant and sets an 'admin' parameter in the web request to ensure
 * that Omeka loads the correct view scripts (and any other theme-specific
 * behavior).  
 *
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

// Flag this as the admin theme. Used by _define_web_root() function in paths.php.
define('ADMIN', true);

// Define the directory and web paths.
include '../paths.php';

// Define the admin theme directory path.
define('THEME_DIR', ADMIN_DIR . DIRECTORY_SEPARATOR . $site['admin_theme']);

$app = new Omeka_Core;
$app->initialize();

// This plugin allows for all functionality that is specific to the 
// admin theme.
$app->getBootstrap()->getResource('FrontController')->registerPlugin(new Omeka_Controller_Plugin_Admin);
$app->run();
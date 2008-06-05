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

// Ladies and Gentlemen, start your timers
define('APP_START', microtime(true));

// Flag this as the admin theme. Used by _define_web_root() function in paths.php.
define('ADMIN', true);

// Define the directory and web paths.
include '../paths.php';

// Define the admin theme directory path.
define('THEME_DIR', ADMIN_DIR . DIRECTORY_SEPARATOR . $site['admin_theme']);

// Initialize Omeka.
require_once 'Omeka/Core.php';
$core = new Omeka_Core;
$core->initialize();

// Let the request know that we want to go through the admin interface.
$core->getRequest()->setParam('admin', true);

// Call the dispatcher which echos the response object automatically
$core->dispatch();

// Ladies and Gentlemen, stop your timers
if ((boolean) $config->debug->timer) {
    echo microtime(true) - APP_START;
}

// We're done here.
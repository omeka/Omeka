<?php
/**
 * Configuration and required files that are shared across testing groups.
 * 
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Testing
 */
define('APPLICATION_ENV', 'testing');

ini_set('max_execution_time', 900);
ini_set('memory_limit', '256M');

// Set the include path and all the constants.
require_once (dirname(dirname(dirname(__FILE__))) . '/paths.php');
require_once 'globals.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('TEST_DIR', dirname(__FILE__));

// Append the testing class library.
define('TEST_LIB_DIR', TEST_DIR . '/libraries');
set_include_path(get_include_path() . PATH_SEPARATOR . TEST_LIB_DIR);

// THEME_DIR is the only constant defined by the bootstrap(s).
// Redefine it here in order to prevent test errors that stem from not defining
// this constant.
// Warning: tests for code that uses THEME_DIR will only use the admin theme dir,
// not the public one.  This could potentially cause subtle breakage in tests.
define('THEME_DIR', join('/', array(BASE_DIR, 'admin', 'themes')));

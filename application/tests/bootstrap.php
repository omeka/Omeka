<?php
/**
 * Configuration and required files that are shared across testing groups.
 * 
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka_Testing
 **/
define('APPLICATION_ENV', 'testing');

ini_set('max_execution_time', 900);
ini_set('memory_limit', '32M');

// Load this while the include path contains the path to PEAR (which has
// conflicts because of naming clashes).
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/Constraint.php';

// Set the include path and all the constants.
require_once '../../paths.php';
require_once 'globals.php';

// THIS TOOK AN UNFORTUNATE AMOUNT OF TIME TO FIGURE OUT, BUT WARNINGS NEED TO
// BE SUPPRESSED IN ORDER TO GET THE DISPATCHING OF 404s IN THE CONTROLLER TESTS TO
// WORK.
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
//error_reporting(E_ALL);
ini_set('display_errors', '1');

define('TEST_DIR', dirname(__FILE__));

// Append the testing class library.
define('TEST_LIB_DIR', TEST_DIR . DIRECTORY_SEPARATOR . 'libraries');
set_include_path(get_include_path() . PATH_SEPARATOR . TEST_LIB_DIR);

// Class loader (copied from Omeka_Core::initializeClassLoader()).
require_once 'Omeka.php';
spl_autoload_register(array('Omeka', 'autoload'));
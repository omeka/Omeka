<?php
// Define the base path
define('BASE_DIR', dirname(__FILE__));

// Shorten the Directory Seperator constant to something reasonable
define('DS', DIRECTORY_SEPARATOR);

// Define some primitive settings so we don't need to load Zend_Config_Ini
$site['application']	= 'application';
$site['library']		= 'library';
$site['controllers']	= 'controllers';
$site['models']			= 'models';
$site['config']			= 'config';

// Set the include path to the library path
// do we want to include the model paths here too? [NA]
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DS.$site['application'].DS.$site['library']);

/**
 * Let's try to make this dynamic.  Zend is already slow, we can speed
 * this up by at least loading Doctrine only when we need it.
require_once 'Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));

* This should use the Zend_Config_Ini obj
$dbh = new PDO(':host=;dbname=', '', '');

Doctrine_Manager::connection($dbh);

// sets a final attribute validation setting to true
Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_VLD, true);
Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_FETCHMODE, Doctrine::FETCH_LAZY);
*/

require_once 'Zend.php';
require_once 'Zend/Config/Ini.php';
Zend::register('config', new Zend_Config_Ini($site['application'].DS.$site['config'].DS.'config.ini'));

// Require the front controller and router
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/RewriteRouter.php';

// Initialize some stuff
$front = Zend_Controller_Front::getInstance();
$router = new Zend_Controller_RewriteRouter();
$router->addConfig(Zend::registry('config'), 'routes');
$front->setRouter($router);
$front->setControllerDirectory($site['application'].DS.$site['controllers']);

// Call the dispatcher and echo the response object
echo $front->dispatch();

// We're done here.
?>
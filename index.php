<?php
// Ladies and Gentlemen, start your timers
define('APP_START', microtime(true));

// Define the base path
define('BASE_DIR', dirname(__FILE__));

// Shorten the Directory Seperator constant to something reasonable
define('DS', DIRECTORY_SEPARATOR);

// Define some primitive settings so we don't need to load Zend_Config_Ini, yet
$site['application']	= 'application';
$site['libraries']		= 'libraries';
$site['controllers']	= 'controllers';
$site['models']			= 'models';
$site['config']			= 'config';

// Define some constants based on those settings
define('MODEL_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['models']);
define('LIB_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries']);
define('APP_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application']);
define('PUBLIC_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'public');
define('PLUGIN_DIR', BASE_DIR . 'public' . DIRECTORY_SEPARATOR . 'plugins' );

// Set the include path to the library path
// do we want to include the model paths here too? [NA]
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DS.$site['application'].DS.$site['libraries']);

/*
 * Let's try to make this dynamic.  Zend is already slow, we can speed
 * this up by at least loading Doctrine only when we need it.
*/
require_once 'Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));

require_once 'Zend/Config/Ini.php';
$db = new Zend_Config_Ini($site['application'].DS.$site['config'].DS.'db.ini', 'database');
Zend::register('db_ini', $db);

$dbh = new PDO($db->type.':host='.$db->host.';dbname='.$db->name, $db->username, $db->password);

Doctrine_Manager::connection($dbh);

// sets a final attribute validation setting to true
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine::ATTR_VLD, true);
$manager->setAttribute(Doctrine::ATTR_FETCHMODE, Doctrine::FETCH_LAZY);

// tack on the search capabilities
require_once 'Kea'.DIRECTORY_SEPARATOR.'SearchListener.php';
$manager->setAttribute(Doctrine::ATTR_LISTENER, new Kea_SearchListener());

// Use Zend_Config_Ini to store the info for the routes and db ini files
require_once 'Zend.php';

Zend::register('routes_ini', new Zend_Config_Ini($site['application'].DS.$site['config'].DS.'routes.ini'));
$config = new Zend_Config_Ini($site['application'].DS.$site['config'].DS.'config.ini');
Zend::register('config_ini', $config);

// Require the front controller and router
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/RewriteRouter.php';

// Initialize some stuff
$front = Zend_Controller_Front::getInstance();
$router = new Zend_Controller_RewriteRouter();
$router->addConfig(Zend::registry('routes_ini'), 'routes');
$front->setRouter($router);

require_once MODEL_DIR.DIRECTORY_SEPARATOR.'PluginTable.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Plugin.php';

//Register all of the active plugins
$plugins = $manager->getTable('Plugin')->activeArray($router);
foreach( $plugins as $plugin )
{
	$front->registerPlugin($plugin);
}

$front->throwExceptions((boolean) $config->site->exceptions);
$front->addControllerDirectory($site['application'].DS.$site['controllers']);

// Call the dispatcher which echos the response object automatically
$front->dispatch();

if ((boolean) $config->site->timer) {
	echo microtime(true) - APP_START;
}
// We're done here.
?>
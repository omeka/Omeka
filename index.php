<?php
// Ladies and Gentlemen, start your timers
define('APP_START', microtime(true));

// Define the base path
define('BASE_DIR', dirname(__FILE__));

// Define some primitive settings so we don't need to load Zend_Config_Ini, yet
$site['application']	= 'application';
$site['libraries']		= 'libraries';
$site['controllers']	= 'controllers';
$site['models']			= 'models';
$site['config']			= 'config';

// Define Web routes
$root = 'http://'.$_SERVER['HTTP_HOST'];
define('WEB_DIR', $root.dirname($_SERVER['PHP_SELF']));
define('WEB_PUBLIC', WEB_DIR.DIRECTORY_SEPARATOR.'public');
define('WEB_ADMIN', WEB_PUBLIC.DIRECTORY_SEPARATOR.'admin');
define('WEB_THEME', WEB_PUBLIC.DIRECTORY_SEPARATOR.'themes');
define('WEB_SHARED', WEB_DIR.DIRECTORY_SEPARATOR.'shared');

// Define some constants based on those settings
define('MODEL_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['models']);
define('LIB_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries']);
define('APP_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application']);
define('PUBLIC_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'public');
define('PLUGIN_DIR', BASE_DIR .DIRECTORY_SEPARATOR. 'public' . DIRECTORY_SEPARATOR . 'plugins' );
define('ADMIN_THEME_DIR', PUBLIC_DIR.DIRECTORY_SEPARATOR.'admin');
define('THEME_DIR', PUBLIC_DIR.DIRECTORY_SEPARATOR.'themes');
define('SHARED_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'shared');

// Set the include path to the library path
// do we want to include the model paths here too? [NA]
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries'].DIRECTORY_SEPARATOR.'Zend_Incubator'.PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries']);

require_once 'Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));

require_once 'Zend/Config/Ini.php';
$db = new Zend_Config_Ini($site['application'].DIRECTORY_SEPARATOR.$site['config'].DIRECTORY_SEPARATOR.'db.ini', 'database');
Zend::register('db_ini', $db);

$dbh = new PDO($db->type.':host='.$db->host.';dbname='.$db->name, $db->username, $db->password);

Doctrine_Manager::connection($dbh);

// sets a final attribute validation setting to true
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine::ATTR_VLD, true);
$manager->setAttribute(Doctrine::ATTR_FETCHMODE, Doctrine::FETCH_LAZY);

// tack on the search capabilities
require_once 'Kea'.DIRECTORY_SEPARATOR.'SearchListener.php';
require_once 'Kea'.DIRECTORY_SEPARATOR.'TimestampListener.php';
$chainListeners = new Doctrine_EventListener_Chain();
$chainListeners->add(new Kea_TimestampListener());
$chainListeners->add(new Kea_SearchListener());
$manager->setAttribute(Doctrine::ATTR_LISTENER, $chainListeners);

// Use Zend_Config_Ini to store the info for the routes and db ini files
require_once 'Zend.php';

// Register the Doctrine Manager
Zend::register('doctrine', $manager);

Zend::register('routes_ini', new Zend_Config_Ini($site['application'].DIRECTORY_SEPARATOR.$site['config'].DIRECTORY_SEPARATOR.'routes.ini'));
$config = new Zend_Config_Ini($site['application'].DIRECTORY_SEPARATOR.$site['config'].DIRECTORY_SEPARATOR.'config.ini', 'site');
Zend::register('config_ini', $config);

// Require the front controller and router
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/RewriteRouter.php';

// Retrieve the ACL from the db, or create a new ACL object
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';
$options = $manager->getTable('option');
$results = $options->findByDql('name LIKE "acl"');
if (count($results) == 0) {
	require_once 'Kea/Acl.php';
	require_once 'Zend/Acl/Role.php';
	require_once 'Zend/Acl/Resource.php';

	$acl = new Kea_Acl();
	$role = new Zend_Acl_Role('super');

	$acl->addRole($role);

	$acl->add(new Zend_Acl_Resource('item'));
	$acl->add(new Zend_Acl_Resource('add'), 'item');
	$acl->add(new Zend_Acl_Resource('edit'), 'item');
	$acl->add(new Zend_Acl_Resource('delete'), 'item');
	$acl->add(new Zend_Acl_Resource('read'), 'item');
	
	$acl->add(new Zend_Acl_Resource('themes'));
	$acl->add(new Zend_Acl_Resource('set'),'themes');
	
	$acl->allow('super');

	$option = new Option;
	$option->name = 'acl';
	$option->value = serialize($acl);
	$option->save();
	Zend::register('acl', $acl);
}
else {
	Zend::register('acl', unserialize($results[0]->value)); 
	$acl = unserialize($results[0]->value);
}

// Initialize some stuff
$front = Kea_Controller_Front::getInstance();
$router = new Zend_Controller_RewriteRouter();
$router->addConfig(Zend::registry('routes_ini'), 'routes');
$front->setRouter($router);

require_once 'Zend/Controller/Request/Http.php';
$request = new Zend_Controller_Request_Http();
Zend::register('request', $request);
$front->setRequest($request);

require_once 'Zend/Controller/Response/Http.php';
$response = new Zend_Controller_Response_Http();
Zend::register('response', $response);
$front->setResponse($response);


require_once MODEL_DIR.DIRECTORY_SEPARATOR.'PluginTable.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Plugin.php';

//Register all of the active plugins
$plugins = $manager->getTable('Plugin')->activeArray($router);
foreach( $plugins as $plugin )
{
	$front->registerPlugin($plugin);
}

$front->throwExceptions((boolean) $config->debug->exceptions);
$front->addControllerDirectory($site['application'].DIRECTORY_SEPARATOR.$site['controllers']);

try{
	$front->dispatch();
}catch(Exception $e) {
	include '404.php';
}

// Call the dispatcher which echos the response object automatically

if ((boolean) $config->debug->timer) {
	echo microtime(true) - APP_START;
}
// We're done here.
?>
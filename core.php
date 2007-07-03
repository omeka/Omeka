<?php
require_once 'Doctrine.php';
//require_once 'Doctrine.compiled.php';
spl_autoload_register(array('Doctrine', 'autoload'));
require_once 'Zend.php';

//Register the various path names so they can be accessed by the app
Zend::register('path_names', $site);

function install_notification() {
	die('Please install Omeka.<a href="'.WEB_ROOT.DIRECTORY_SEPARATOR.'install/install.php">Click here to install Omeka.</a>');
}

require_once 'Zend/Config/Ini.php';
$db = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'db.ini', 'database');
Zend::register('db_ini', $db);

$dsn = 'mysql:host='.$db->host.';dbname='.$db->name;
if(isset($db->port)) {
	$dsn .= "port=" . $db->port;
}
try {
	$dbh = new PDO($dsn, $db->username, $db->password);
} catch (Exception $e) {
	install_notification();
}

//Pull the options from the DB
$option_stmt = $dbh->query('SELECT * FROM options');
if(!$option_stmt) {
	install_notification();
}
$option_array = $option_stmt->fetchAll();

// ****** CHECK TO SEE IF OMEKA IS INSTALLED ****** 
if(!count($option_array)) {
	install_notification();
}

//Save the options so they can be accessed
$options = array();
foreach ($option_array as $opt) {
	$options[$opt['name']] = $opt['value'];
}
Zend::register('options',$options);

function get_option($name) {
		$options = Zend::Registry('options');
		return $options[$name];
}

Doctrine_Manager::connection($dbh);

// sets a final attribute validation setting to true
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine::ATTR_VLD, true);

//@todo Uncomment this prior to production release for increase in speed
//$manager->setAttribute(Doctrine::ATTR_CREATE_TABLES, false);
$manager->setAttribute(Doctrine::ATTR_FETCHMODE, Doctrine::FETCH_LAZY);
$manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);

// Register the Doctrine Manager
Zend::register('doctrine', $manager);

//Check the current migration # in the DB against the hardcoded #
//Migrate the DB if necessary and exit
if(!isset($options['migration']) or $options['migration'] != OMEKA_MIGRATION) {
	$fromVersion = $options['migration'] or $fromVersion = 0;
	$toVersion = OMEKA_MIGRATION;
	require_once 'Kea/Upgrader.php';
	$upgrader = new Kea_Upgrader($manager, $fromVersion, $toVersion);
	exit;
}


$config = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'config.ini', 'site');
Zend::register('config_ini', $config);

if(isset($config->log)) {
	require_once LIB_DIR.DIRECTORY_SEPARATOR.'Kea'.DIRECTORY_SEPARATOR.'Logger.php';
	$logger = new Kea_Logger;

	if(isset($config->log->sql) && $config->log->sql) {
		$logger->setSqlLog(LOGS_DIR.DIRECTORY_SEPARATOR.'sql.log');
		$logger->activateSqlLogging(true);	
	}
	if(isset($config->log->errors) && $config->log->errors) {
		$logger->setErrorLog(LOGS_DIR.DIRECTORY_SEPARATOR.'errors.log');
		$logger->activateErrorLogging(true);
	}
}


// tack on the search capabilities
require_once 'Kea'.DIRECTORY_SEPARATOR.'TimestampListener.php';
$chainListeners = new Doctrine_EventListener_Chain();

$chainListeners->add(new Kea_TimestampListener());

$manager->setAttribute(Doctrine::ATTR_LISTENER, $chainListeners);



// Use Zend_Config_Ini to store the info for the routes and db ini files
require_once 'Zend.php';

require_once 'Kea.php';
spl_autoload_register(array('Kea', 'autoload'));



Zend::register('routes_ini', new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'routes.ini'));

// Require the front controller and router
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/RewriteRouter.php';


// Retrieve the ACL from the db, or create a new ACL object
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';



include 'acl.php';

Zend::register('acl', $acl);

require_once 'Zend/Auth.php';
require_once 'Kea/Auth/Adapter.php';

$authPrefix = get_option('auth_prefix');

//Leave this in for development, take it out for release
if(!$authPrefix) {
	$authPrefix = md5(mt_rand());
	$prefixOption = new Option();
	$prefixOption->name = 'auth_prefix';
	$prefixOption->value = $authPrefix;
	$prefixOption->save();
}

//Set up the authentication mechanism with the specially generated prefix
$auth = new Zend_Auth(new Kea_Auth_Adapter(), true, $authPrefix);

//Register the Authentication mechanism to be able to share it
Zend::register('auth', $auth);


// Initialize some stuff
$front = Kea_Controller_Front::getInstance();
$router = new Zend_Controller_RewriteRouter();
$router->addConfig(Zend::registry('routes_ini'), 'routes');
$front->setRouter($router);

// Adds the static routes that we all about and shit (rhyme unintentional, please kill me)
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Route.php';
$staticRoutes = $manager->getTable('Route')->findStatic();
foreach ($staticRoutes as $route) {
	$router->addRoute($route['name'], new Zend_Controller_Router_StaticRoute($route['route'], array('controller'=>'static', 'action'=>'findStatic', 'route'=>$route)));
}

require_once 'Zend/Controller/Request/Http.php';
$request = new Zend_Controller_Request_Http();

// Removed 3/9/07 n8
//Zend::register('request', $request);
$front->setRequest($request);

require_once 'Zend/Controller/Response/Http.php';
$response = new Zend_Controller_Response_Http();
// Removed 3/9/07 n8
//Zend::register('response', $response);
$front->setResponse($response);

#############################################
# INITIALIZE PLUGINS
#############################################
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'PluginTable.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Plugin.php';

//Register all of the active plugins
$plugins = $manager->getTable('Plugin')->activeArray($router);
foreach( $plugins as $plugin )
{
	$front->registerPlugin($plugin);
}

$front->throwExceptions((boolean) true);

$front->addControllerDirectory(CONTROLLER_DIR);
?>
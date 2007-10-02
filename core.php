<?php
require_once 'globals.php';
require_once 'Doctrine.php';
//require_once 'Doctrine.compiled.php';
spl_autoload_register(array('Doctrine', 'autoload'));

//Register the various path names so they can be accessed by the app
Zend_Registry::set('path_names', $site);

require_once 'Zend/Config/Ini.php';
$db = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'db.ini', 'database');
Zend_Registry::set('db_ini', $db);

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
Zend_Registry::set('options', $options);

Doctrine_Manager::connection($dbh);

// sets a final attribute validation setting to true
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine::ATTR_VLD, true);

//@todo Uncomment this prior to production release for increase in speed
$manager->setAttribute(Doctrine::ATTR_FETCHMODE, Doctrine::FETCH_LAZY);
$manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);

// Register the Doctrine Manager
Zend_Registry::set('doctrine', $manager);

//Check the current migration # in the DB against the hardcoded #
//Migrate the DB if necessary and exit
if(!isset($options['migration'])) {
	$dbh->query("INSERT INTO `options` (name, value) VALUES ('migration',0)");
	$options['migration'] = 0;
}

if((int) $options['migration'] < OMEKA_MIGRATION) {
	$fromVersion = $options['migration'] or $fromVersion = 0;
	$toVersion = OMEKA_MIGRATION;
	require_once 'Omeka/Upgrader.php';
	$upgrader = new Omeka_Upgrader($manager, $fromVersion, $toVersion);
	exit;
}


$config = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'config.ini', 'site');
Zend_Registry::set('config_ini', $config);

if(isset($config->log)) {
	require_once LIB_DIR.DIRECTORY_SEPARATOR.'Omeka'.DIRECTORY_SEPARATOR.'Logger.php';
	$logger = new Omeka_Logger;

	if(isset($config->log->sql) && $config->log->sql) {
		$logger->setSqlLog(LOGS_DIR.DIRECTORY_SEPARATOR.'sql.log');
		$logger->activateSqlLogging(true);	
	}
	if(isset($config->log->errors) && $config->log->errors) {
		$logger->setErrorLog(LOGS_DIR.DIRECTORY_SEPARATOR.'errors.log');
		$logger->activateErrorLogging(true);
	}
}

//Setup the ACL
include 'acl.php';

Zend_Registry::set('acl', $acl);

//Activate the plugins
require_once 'plugins.php';
$plugin_broker = new PluginBroker;

$chainListeners = new Doctrine_EventListener_Chain();

$manager->setAttribute(Doctrine::ATTR_LISTENER, $chainListeners);


// Use Zend_Config_Ini to store the info for the routes and db ini files
require_once 'Omeka.php';
spl_autoload_register(array('Omeka', 'autoload'));

Zend_Registry::set('routes_ini', new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'routes.ini', null));

// Require the front controller and router
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Router/Rewrite.php';


require_once 'Item.php';
require_once 'Option.php';

include 'auth.php';

//Register the Authentication mechanism to be able to share it
Zend_Registry::set('auth', $auth);


// Initialize some stuff
$front = Omeka_Controller_Front::getInstance();
$router = new Zend_Controller_Router_Rewrite();
$router->addConfig(Zend_Registry::get('routes_ini'), 'routes');
fire_plugin_hook('loadRoutes', $router);

$router->setFrontController($front);
$front->setRouter($router);

$front->getDispatcher()->setFrontController($front);

//Disable the ViewRenderer until we can refactor Omeka codebase to use it
$front->setParam('noViewRenderer', true);

require_once 'Zend/Controller/Request/Http.php';
$request = new Zend_Controller_Request_Http();

// Removed 3/9/07 n8
//Zend_Registry::set('request', $request);
$front->setRequest($request);

require_once 'Zend/Controller/Response/Http.php';
$response = new Zend_Controller_Response_Http();
// Removed 3/9/07 n8
//Zend_Registry::set('response', $response);
$front->setResponse($response);

$front->throwExceptions((boolean) true);

//$front->addControllerDirectory(array('default'=>CONTROLLER_DIR));
$front->setControllerDirectory(CONTROLLER_DIR);
?>
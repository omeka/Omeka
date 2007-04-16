<?php
require_once 'Doctrine.php';
//require_once 'Doctrine.compiled.php';
spl_autoload_register(array('Doctrine', 'autoload'));
require_once 'Zend.php';


/**
 * Check for a config file which, if not present implies that the
 * app has not been installed.
 */
if (!file_exists(CONFIG_DIR.DIRECTORY_SEPARATOR.'db.ini')) {
	echo 'It looks like you have not properly setup Omeka to run.  <a href="'.WEB_ROOT.DIRECTORY_SEPARATOR.'install/install.php">Click here to install Omeka.</a>';
	exit;
}

require_once 'Zend/Config/Ini.php';
$db = new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'db.ini', 'database');
Zend::register('db_ini', $db);

$dbh = new PDO($db->type.':host='.$db->host.';dbname='.$db->name, $db->username, $db->password);

Doctrine_Manager::connection($dbh);

// sets a final attribute validation setting to true
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine::ATTR_VLD, true);

//@todo Uncomment this prior to production release for increase in speed
//$manager->setAttribute(Doctrine::ATTR_CREATE_TABLES, false);

$manager->setAttribute(Doctrine::ATTR_FETCHMODE, Doctrine::FETCH_LAZY);
$manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);

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
require_once 'Kea'.DIRECTORY_SEPARATOR.'SearchListener.php';
require_once 'Kea'.DIRECTORY_SEPARATOR.'TimestampListener.php';
$chainListeners = new Doctrine_EventListener_Chain();
$chainListeners->add(new Kea_TimestampListener());
$chainListeners->add(new Kea_SearchListener());

$manager->setAttribute(Doctrine::ATTR_LISTENER, $chainListeners);



// Use Zend_Config_Ini to store the info for the routes and db ini files
require_once 'Zend.php';

require_once 'Kea.php';
spl_autoload_register(array('Kea', 'autoload'));

// Register the Doctrine Manager
Zend::register('doctrine', $manager);

Zend::register('routes_ini', new Zend_Config_Ini(CONFIG_DIR.DIRECTORY_SEPARATOR.'routes.ini'));

// Require the front controller and router
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/RewriteRouter.php';


// Retrieve the ACL from the db, or create a new ACL object
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';

/**
 * This is a global function, which may seem bad but its really the only one that needs to be global
 *
 * @return void
 **/
function get_option($name) {
	if(Zend::isRegistered('options')) {
		$options = Zend::Registry('options');
		return $options[$name];
	}else {
		$q = new Doctrine_Query;
		$array = $q->parseQuery("SELECT o.name,o.value FROM Option o")->execute(array(),Doctrine::FETCH_ARRAY);
		foreach ($array as $k => $v) {
			$options[$v['o']['name']] = $v['o']['value'];
		}		
		Zend::register('options',$options);
		return $options[$name];
	}
}

$acl = unserialize(get_option('acl'));
Zend::register('acl', $acl);

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
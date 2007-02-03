<?php
/**
 * The following is taken from Wordpress 2.0
 * and is licensed under the GNU Public License.
 * If a copy of the licsense was not included in
 * this distribution you may email XXXXXXXXXXXXXXX
 * for the license.
 */

// Turn register globals off
function unregister_GLOBALS() {
	if (!ini_get('register_globals')) return;

	if (isset($_REQUEST['GLOBALS'])) die('GLOBALS overwrite attempt detected');

	// Variables that shouldn't be unset
	$noUnset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES', 'table_prefix');
	
	$input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach ($input as $k => $v)
		if (!in_array($k, $noUnset) && isset($GLOBALS[$k]))
			unset($GLOBALS[$k]);
}
unregister_GLOBALS();

// Fix for IIS, which doesn't set REQUEST_URI
if (empty($_SERVER['REQUEST_URI'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME']; // Does this work under CGI?
	
	// Append the query string if it exists and isn't null
	if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

// Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests
if (isset($_SERVER['SCRIPT_FILENAME']) && (strpos($_SERVER['SCRIPT_FILENAME'], 'php.cgi') == strlen($_SERVER['SCRIPT_FILENAME']) - 7 ))
	$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];

// Fix for Dreamhost and other PHP as CGI hosts
if (strstr( $_SERVER['SCRIPT_NAME'], 'php.cgi' ))
	unset($_SERVER['PATH_INFO']);

// Fix empty PHP_SELF
$PHP_SELF = $_SERVER['PHP_SELF'];
if (empty($PHP_SELF))
	$_SERVER['PHP_SELF'] = $PHP_SELF = preg_replace("/(\?.*)?$/",'',$_SERVER["REQUEST_URI"]);

if (!(phpversion() >= '5.1'))
	die('Your server is running PHP version ' . phpversion() . ' but Sitebuilder requires at least 5.1');

if (!extension_loaded('mysql'))
	die('Your PHP installation appears to be missing the MySQL which is required for Sitebuilder.');
/**
 * End of Wordpress import
 * Thanks guys!
 */

// Set the include path
set_include_path(get_include_path() . ':'.KEA_ROOT.'/library' . ':'.KEA_ROOT.'/app/models' . ':'.KEA_ROOT.'/app/filters' . ':'.KEA_ROOT.'/app/lib');

// Handle uncaught exceptions by redirecting to a 404 page
function uncaught_exception_handler($e) {
	$out = ob_get_contents();
	echo $out . $e->__toString();
	/**
	 * We can't include this call to the 404 page because that page runs 
	 * application code, and if there is an application failure, it var_dumps the 
	 * uncaught exception rather than a nice clean error message - [KBK]
	 */
	//include( ABS_CONTENT_DIR.DS.'404.php');
	exit();
}

// Sets the function for top level uncaught exceptions
set_exception_handler("uncaught_exception_handler");

// Set the default timezone, a PHP 5 thing
date_default_timezone_set('America/New_York');

// DB settings

$db_host		= "localhost";
$db_name		= "sb-v2";
$db_user		= "n8agrin";
$db_password	= "dudeman";

/*
// CHNM Settings
$db_host		= "mysql.localdomain";
$db_name		= "sitebuilder_doctrine";
$db_user		= "sitebuilder";
$db_password	= "XEddVNrwVYAGvrTW";
*/

require_once "library/Doctrine/Doctrine.php";
spl_autoload_register(array('Doctrine', 'autoload'));  // autoload. it works, bitches
$dbh = new Doctrine_Db('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_password);
Doctrine_Manager::connection($dbh);
Doctrine_Manager::getInstance()->setAttribute(Doctrine::ATTR_VLD, true);

// Includes
require_once 'debug.php';
require_once 'constants.php';
require_once 'stdlib.php';
?>
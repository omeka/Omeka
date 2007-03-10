<?php
/**
 * Set some base config stuff
 */
define('WEB_ROOT', 'http://'.$_SERVER['HTTP_HOST'].dirname(dirname($_SERVER['PHP_SELF'])));

// Define the base path
define('BASE_DIR', dirname(dirname(__FILE__)));
define('THIS_DIR', dirname(__FILE__));

// Define some primitive settings so we don't need to load Zend_Config_Ini, yet
$site['application']	= 'application';
$site['libraries']		= 'libraries';
$site['controllers']	= 'controllers';
$site['models']			= 'models';
$site['config']			= 'config';

// Define Web routes
$root = 'http://'.$_SERVER['HTTP_HOST'];
define('WEB_DIR', $root.dirname($_SERVER['PHP_SELF']));
define('WEB_THEME', WEB_DIR.DIRECTORY_SEPARATOR.'themes');

// Define some constants based on those settings
define('MODEL_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['models']);
define('LIB_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application'].DIRECTORY_SEPARATOR.$site['libraries']);
define('APP_DIR', BASE_DIR.DIRECTORY_SEPARATOR.$site['application']);
define('PUBLIC_DIR', BASE_DIR.DIRECTORY_SEPARATOR.'public');
define('PLUGIN_DIR', BASE_DIR .DIRECTORY_SEPARATOR. 'public' . DIRECTORY_SEPARATOR . 'plugins' );
define('ADMIN_THEME_DIR', PUBLIC_DIR.DIRECTORY_SEPARATOR.'admin');
define('THEME_DIR', PUBLIC_DIR.DIRECTORY_SEPARATOR.'themes');

/**
 * Check to see if the db has already been setup
 */
if (file_exists(BASE_DIR.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'db.ini')) {
	echo 'Omeka has already been setup. This file and the install directory should be deleted by an administrator. <a href="'.WEB_ROOT.'">Click here to go to this site.</a>';
	exit;
}

/**
 * What needs to be done on an install?
 * 1) Setup the db connection
 * 2) Write the db config file
 * 3) Create all the tables
 */
$display_form = true;
if (isset($_REQUEST['install_submit'])) {
	// try to connect to the db
	$db = $_REQUEST['db'];
	try{
		$dbh = new PDO($db['type'].':host='.$db['host'].';dbname='.$db['name'], $db['username'], $db['password']);
		if (!$dbh instanceof PDO) {
			throw new Exception('No database connection could be created');
		}
		
		// Create the db if it doesn't exist
		
		// YEY! the db connection worked, let's save it
		$db_config = ";;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
; DB Configuration
;
; phpDoctrine can support many different types of databases.
; Configurations differ based on the type of database needed.
; Below are some examples:
;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
[database]
type     = ".$db['type']."
host     = ".$db['host']."
username = ".$db['username']."
password = ".$db['password']."
name     = ".$db['name']."
";
		$f = fopen(BASE_DIR.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'db.ini', 'w');
		fwrite($f, $db_config);
		fclose($f);		

		// Set the include path
		set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'libraries');

		// Setup some properties in the db
		require_once 'Doctrine.php';
		spl_autoload_register(array('Doctrine', 'autoload'));
		Doctrine_Manager::connection($dbh);
		$manager = Doctrine_Manager::getInstance();
		
		// Retrieve the ACL from the db, or create a new ACL object
		require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Option.php';
		$options = $manager->getTable('option');
		
		// Create the ACL option
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
		
		// Create the project name option
		$project_title = new Option;
		$project_title->name = 'project_title';
		$project_title->value = $_REQUEST['project']['name'];
		$project_title->save();
		
		// Set the default themes
		$admin = new Option();
		$admin->name = 'admin_theme';
		$admin->value = 'default';
		
		$theme = new Option();
		$theme->name = 'public_theme';
		$theme->value = 'default';
		
		$admin->save();
		$theme->save();
		
		echo 'hooray! the db is setup and you are ready to role.  <a href="'.WEB_ROOT.'">check out your site here!</a>';
		$display_form = false;

	} catch(Exception $e) {
		echo $e->getMessage();
		$display_form = true;
	}
}

if ($display_form == true) {
?>
<form action="install.php" method="post" accept-charset="utf-8">
	<h1>Project Info</h1>
	Project Name:<input type="text" name="project[name]" value="" id="project[name]"/>
	
	<h1>Database info</h1>
	Host:<input type="text" name="db[host]" value="localhost" id="host"/><br/>
	Username:<input type="text" name="db[username]" value="root" id="username"/><br/>
	Password:<input type="password" name="db[password]" value="" id="password"/><br/>
	Port:<input type="text" name="db[port]" value="" id="port"/><br/>
	DB Name:<input type="text" name="db[name]" value="omeka" id="name"/><br/>
	DB Type:<input type="text" name="db[type]" value="mysql" id="type"/><br/>
	<p><input type="submit" value="Continue" name="install_submit"></p>
</form>
<?php } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Omeka Installation</title>

<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Stylesheets -->
<link rel="stylesheet" media="screen" href="install.css" />
</head>

<body>
<?php
require_once '../paths.php';
/* Steps for installation:
 * 1) Fill out your db.ini file by hand
 * 2) Submit this form with all the relevant settings
 * 3) Create all the tables
 */
require_once 'Zend.php';
require_once 'Zend/Config/Ini.php';

try {
	//Check for the config file
	$config_file = CONFIG_DIR . DIRECTORY_SEPARATOR . 'db.ini';
	if (!file_exists($config_file)) {
		throw new Exception('Your Omeka database configuration file is missing.');
	}
	if (!is_readable($config_file)) {
		throw new Exception('Your Omeka database configuration file cannot be read by the application.');
	}

	$config = new Zend_Config_Ini($config_file);

	$db = $config->database->asArray();

	//Fail on improperly configured db.ini file
	if (!isset($db['host']) or ($db['host'] == 'XXXXXXX')) {
		throw new Exception('Your Omeka database configuration file has not been set up properly.');
	}

	//Create the DSN
	$dsn = 'mysql:host='.$db['host'].';dbname='.$db['name'];
	if(isset($db['port'])) {
		$dsn .= 'port='.$db['port'].';';
	}

	//PDO Connection
	//@todo Add "port" option to db.ini and all PDO connections within the app
	$dbh = new PDO($dsn, $db['username'], $db['password']);
	if (!$dbh instanceof PDO) {
		throw new Exception('No database connection could be created');
	}

	// Setup Doctrine
	require_once 'Doctrine.php';
	spl_autoload_register(array('Doctrine', 'autoload'));
	Doctrine_Manager::connection($dbh);
	$manager = Doctrine_Manager::getInstance();
	Zend::register('doctrine', $manager);

	//Build tables automagically
	$manager->setAttribute(Doctrine::ATTR_CREATE_TABLES, true);

	//Check if the options table is filled (if so, Omeka already set up so die)
	require_once 'Option.php';
	$options = $manager->getTable('Option')->findAll();
	if (count($options)) {
		throw new Exception('Omeka has already been configured.  Please remove this install directory.');
	}
	
	// Use the "which" command to auto-detect the path to ImageMagick;
	// redirect std error to where std input goes, which is nowhere
	// see http://www.unix.org.ua/orelly/unix/upt/ch45_21.htm
	$output = shell_exec('which convert 2>&0');
	$path_to_convert = ($output !== NULL) ? trim($output) : FALSE;
	
} catch (Exception $e) {
	die($e->getMessage() . '  Please refer to Omeka documentation for help.');
}



$display_form = true;

//Try to actually install the thing
if (isset($_REQUEST['install_submit'])) {
	
	try {
		//Validate the FORM POST
		$validation = array(
					'administrator_email' => "/^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/",
					'thumbnail_constraint' => "/\d+/",
					'fullsize_constraint' => "/\d+/",
					'username' => "/^[\w\d\_\.]{4,}$/",		//At least 4 characters, _ . alphanumeric allowed
					'password' => "/^.{4,}$/");				//At least 4 characters (all allowed)
			
		foreach ($validation as $key => $validator) {
			if($validator) {
				if(!preg_match($validator, $_POST[$key])) {
					throw new Exception( $key . ' was not configured properly.  Please try again.' );
				}
			}
		}
				
		// Build the tables explicitly
		$installSQL = file_get_contents('install.sql');
		
		$manager = Doctrine_Manager::getInstance();
		$conn = $manager->connection();
		$conn->execute($installSQL);
		
		// Create the default user
		require_once 'User.php';
		require_once 'Person.php';
		
		$userTable = $manager->getTable('User')->getTableName();
		$entityTable = $manager->getTable('Entity')->getTableName();
		
		$entitySql = "INSERT INTO $entityTable (type) VALUES (?)";
		$conn->execute($entitySql, array("Person"));
		
		$userSql = "INSERT INTO $userTable (username, password, active, role, entity_id) VALUES (?, SHA1(?), 1, 'super', LAST_INSERT_ID())";
		$conn->execute($userSql, array($_REQUEST['username'], $_REQUEST['password']));
		
	
		// Namespace for the authentication session (to prevent clashes on shared servers)
		require_once 'Option.php';
		$auth_prefix = new Option();
		$auth_prefix->name = 'auth_prefix';
		$auth_prefix->value = md5(mt_rand());
		$auth_prefix->save();
		
		// Add the migration option to the DB
		$migration = new Option;
		$migration->name = 'migration';
		$migration->value = OMEKA_MIGRATION;
		$migration->save();
		
		// Add the settings to the db
		$settings = array('administrator_email', 'copyright', 'site_title', 'author', 'description', 'thumbnail_constraint', 'fullsize_constraint', 'path_to_convert');
		foreach ($settings as $v) {
			$setting = new Option;
			$setting->name = $v;
			$setting->value = $_POST[$v];
			$setting->save();
		}
		
		// Set the default themes
		$admin = new Option();
		$admin->name = 'admin_theme';
		$admin->value = 'default';
		
		$theme = new Option();
		$theme->name = 'public_theme';
		$theme->value = 'default';
		
		$admin->save();
		$theme->save();

		
		echo 'hooray! the db is setup and you are ready to roll.  <a href="'.dirname(dirname($_SERVER['REQUEST_URI'])).'">check out your site here!</a>';
		$display_form = false;

	} catch(Exception $e) {
		echo $e->getMessage();
//		echo $e->getTraceAsString();
		$display_form = true;
	}
}

if ($display_form == true):
?>
<form action="install.php" method="post" accept-charset="utf-8" id="install-form">
	<h1>Site Settings</h1>
	<label for="site_title">Site Name:</label>
	<input type="text" name="site_title" id="site_title" value="<?php echo $_POST['site_title']; ?>" />
	<label for="admin_email">Administrator Email (required for form emails):</label>
	<input type="text" name="administrator_email" id="admin_email" value="<?php echo $_POST['administrator_email']; ?>" />
	<label for="copyright">Copyright Info:</label>
	<input type="text" name="copyright" id="copyright" value="<?php echo $_POST['copyright']; ?>" />
	<label for="author">Author Info:</label>
	<input type="text" name="author" id="author" value="<?php echo $_POST['author']; ?>" />
	<label for="description">Site Description:</label>
	<textarea name="description" id="description"><?php echo $_POST['description']; ?></textarea>
	<label for="thumbnail_constraint">Maximum Thumbnail Size Constraint (px):</label>
	<input type="text" name="thumbnail_constraint" id="thumbnail_constraint" value="<?php echo (!empty($_POST['thumbnail_constraint']) ? $_POST['thumbnail_constraint'] : 150); ?>" />
	<label for="fullsize_constraint">Maximum Fullsize Image Size Constraint (px)</label> 
	<input type="text" name="fullsize_constraint" id="fullsize_constraint" value="<?php echo (!empty($_POST['fullsize_constraint']) ? $_POST['fullsize_constraint'] : 600); ?>" />
	<label for="path_to_convert">Imagemagick Binary Path:</label>
	<?php
	if ($path_to_convert) {
		echo '
	<input type="hidden" name="path_to_convert" id="path_to_convert" value="'.$path_to_convert.'" />
	<p>'.$path_to_convert.' (found automatically)</p>';
	} else {
		echo '
	<input type="text" name="path_to_convert" id="path_to_convert" value="'.$_POST['path_to_convert'].'" />';
	}
	?>
	
	<h1>Default Super User Account</h1>
	<label for="username">Username:</label><input type="text" name="username" value="<?php echo $_POST['username']; ?>" />
	<label for="password">Password:</label><input type="password" name="password" value="<?php echo $_POST['password']; ?>"/>
	<p><input type="submit" value="Continue" name="install_submit"></p>
</form>
<?php endif; ?>

</body>
</html>
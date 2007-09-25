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
	<div id="wrap">
<?php
require_once '../paths.php';
/* Steps for installation:
 * 1) Fill out your db.ini file by hand
 * 2) Submit this form with all the relevant settings
 * 3) Create all the tables
 */
require_once 'Zend.php';
require_once 'Zend/Config/Ini.php';
require_once 'plugins.php';
require_once 'globals.php';
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

	//Build the database if necessary
	$res = $dbh->query("SHOW TABLES");
	$tables = $res->fetchAll();
	
	if(empty($tables)) {
		// Build the tables explicitly
		$installSQL = file_get_contents('install.sql');

		$stmts = explode(';', $installSQL);

		foreach ($stmts as $sql) {
			$dbh->query($sql);
		}
	}

	// Setup Doctrine
	require_once 'Doctrine.php';
	spl_autoload_register(array('Doctrine', 'autoload'));
	Doctrine_Manager::connection($dbh);
	$manager = Doctrine_Manager::getInstance();
	Zend::register('doctrine', $manager);

	//Check if the options table is filled (if so, Omeka already set up so die)
	require_once 'Option.php';
	$options = $manager->getTable('Option')->findAll();
	if (count($options)) {
		throw new Exception('<h1>Omeka Already Installed</h1><p>It looks like Omeka has already been installed. You can remove the &#8220;install&#8221; directory for security reasons.</p>');
	}
	
	// Use the "which" command to auto-detect the path to ImageMagick;
	// redirect std error to where std input goes, which is nowhere
	// see http://www.unix.org.ua/orelly/unix/upt/ch45_21.htm
	$output = shell_exec('which convert 2>&0');
	$path_to_convert = ($output !== NULL) ? trim($output) : FALSE;
	
} catch (Exception $e) {
	die($e->getMessage() . '<p>Please refer to Omeka documentation for help.</p>');
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
		
		foreach ($_POST as $key => $value) {
			$_POST[$key] = strip_slashes($value);
		}
			
		foreach ($validation as $key => $validator) {
			if($validator) {
				if(!preg_match($validator, $_POST[$key])) {
					throw new Exception( $key . ' was not configured properly.  Please try again.' );
				}
			}
		}
		
		$conn = Doctrine_Manager::getInstance()->connection();
		
		// Create the default user
		require_once 'User.php';
		require_once 'Person.php';
		
		$userTable = $manager->getTable('User')->getTableName();
		$entityTable = $manager->getTable('Entity')->getTableName();
		
		$entitySql = "INSERT INTO $entityTable (type, email, first_name, last_name) VALUES (?, ?, ?, ?)";
		$conn->execute($entitySql, array("Person", $_POST['super_email'], 'Super', 'User'));
		
		$userSql = "INSERT INTO $userTable (username, password, active, role, entity_id) VALUES (?, SHA1(?), 1, 'super', LAST_INSERT_ID())";
		$conn->execute($userSql, array($_POST['username'], $_POST['password']));
		
	
		// Namespace for the authentication session (to prevent clashes on shared servers)
		$optionTable = $manager->getTable('Option')->getTableName();
		
		$optionSql = "INSERT INTO $optionTable (name, value) VALUES (?,?)";
		$conn->execute($optionSql, array('auth_prefix', md5(mt_rand())));
		$conn->execute($optionSql, array('migration', OMEKA_MIGRATION));
		
		// Add the settings to the db
		$settings = array('administrator_email', 'copyright', 'site_title', 'author', 'description', 'thumbnail_constraint', 'square_thumbnail_constraint', 'fullsize_constraint', 'path_to_convert');
		foreach ($settings as $v) {
			$conn->execute($optionSql, array($v, $_POST[$v]));
		}
		
		$conn->execute($optionSql, array('admin_theme', 'default'));
		$conn->execute($optionSql, array('public_theme', 'default'));

		echo '<div id="intro">';
		echo '<h1>All Finished!</h1>';
		echo '<p>Omeka&#8217;s database is setup and you are ready to roll. <a href="'.dirname($_SERVER['REQUEST_URI']).'">Check out your site!</a></p>';
		echo '</div>';
		$display_form = false;

	} catch(Exception $e) {
		echo $e->getMessage();
//		echo $e->getTraceAsString();
		$display_form = true;
	}
}

if ($display_form == true):
?>
<div id="intro">
<h1>Welcome to Omeka!</h1>
<p>To complete the installation process, please fill out the form below:</p>
</div>
<form method="post" accept-charset="utf-8" id="install-form">
	<fieldset>
	<legend>Site Settings</legend>
	<div class="field">
	<label for="site_title">Site Name</label>
	<input type="text" name="site_title" class="textinput" id="site_title" value="<?php echo htmlentities($_POST['site_title']); ?>" />
	</div>
	<div class="field">
	<label for="admin_email">Administrator Email (required for form emails)</label>
	<input type="text" name="administrator_email" class="textinput" id="admin_email" value="<?php echo htmlentities($_POST['administrator_email']); ?>" />
	</div>
	<div class="field">
	<label for="copyright">Copyright Info</label>
	<input type="text" name="copyright" class="textinput" id="copyright" value="<?php echo htmlentities($_POST['copyright']); ?>" />
	</div>
	<div class="field">
	<label for="author">Author Info</label>
	<input type="text" class="textinput" name="author" id="author" value="<?php echo $_POST['author']; ?>" />
	</div>
	<div class="field">
	<label for="description">Site Description</label>
	<textarea name="description" class="textinput" id="description"><?php echo htmlentities($_POST['description']); ?></textarea>
	</div>
	<div class="field">
	<label for="thumbnail_constraint">Maximum Thumbnail Size Constraint (px)</label>
	<input type="text" class="textinput" name="thumbnail_constraint" id="thumbnail_constraint" value="<?php echo (!empty($_POST['thumbnail_constraint']) ? htmlentities($_POST['thumbnail_constraint']) : 150); ?>" />
	</div>
	<div class="field">
	<label for="square_thumbnail_constraint">Maximum Square Thumbnail Size Constraint (px)</label>
	<input type="text" class="textinput" name="square_thumbnail_constraint" id="square_thumbnail_constraint" value="<?php echo (!empty($_POST['square_thumbnail_constraint']) ? htmlentities($_POST['square_thumbnail_constraint']) : 100); ?>" />
	</div>
	<div class="field">
	<label for="fullsize_constraint">Maximum Fullsize Image Size Constraint (px)</label> 
	<input type="text" class="textinput" name="fullsize_constraint" id="fullsize_constraint" value="<?php echo (!empty($_POST['fullsize_constraint']) ? htmlentities($_POST['fullsize_constraint']) : 600); ?>" />
	</div>
	<div class="field">
	<label for="path_to_convert">Imagemagick Binary Path</label>
	<input type="text" name="path_to_convert" class="textinput" id="path_to_convert" value="
	<?php if ($path_to_convert) { 
			echo "$path_to_convert\" /> <p>$path_to_convert (found automatically)</p>";
		  } else {
			echo htmlentities($_POST['path_to_convert'])."\" />";
		} ?>
	</div>
	</fieldset>
	<fieldset>
	<legend>Default Super User Account</legend>
	<div class="field">
	<label for="username">Username</label>
	<input type="text" class="textinput" name="username" value="<?php echo htmlentities($_POST['username']); ?>" />
	</div>
	<div class="field">
	<label for="password">Password</label>
	<input class="textinput" type="password" name="password" value="<?php echo htmlentities($_POST['password']); ?>"/>
	</div>
	<div class="field">
		<label for="super_email">Email</label>
		<input class="textinput" type="text" name="super_email" id="super_email" value="<?php echo htmlentities($_POST['super_email']); ?>">
	</div>
	
	</fieldset>
	<p><input type="submit" value="Continue" name="install_submit" /></p>
</form>
<?php endif; ?>
</div>
</body>
</html>
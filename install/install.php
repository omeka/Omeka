<?php 
require_once '../paths.php';
/* Steps for installation:
 * 1) Fill out your db.ini file by hand
 * 2) This form detects a database connection, builds the tables
 * 3) Submit this form with all the relevant settings, they are saved to DB 
 */

require_once 'Omeka/Core.php';
$core = new Omeka_Core;
$core->sanitizeMagicQuotes();
$core->initializeClassLoader();
$core->initializeConfigFiles();
$core->initializeDb();

try {
    $db = $core->getDb();
	
	//Build the database if necessary
	$show_tables_sql = "SHOW TABLES";
	//Ensure that we don't confuse Omeka if there are already tables in the DB
	if($db->prefix) $show_tables_sql .= " LIKE '$db->prefix%'";
	$res = $db->query($show_tables_sql);
	$tables = $res->fetchAll();
	
	if(empty($tables)) {
		// Build the tables explicitly
		include 'install.sql.php';
		$db->execBlock($install_sql);
	}

	//Check if the options table is filled (if so, Omeka already set up so die)
	require_once 'Option.php';
	$options = $db->getTable('Option')->findAll();
	if (count($options)) {
		throw new Exception('<h1>Omeka Already Installed</h1><p>It looks like Omeka has already been installed. You can remove the &#8220;install&#8221; directory for security reasons.</p>');
	}
	
	// Use the "which" command to auto-detect the path to ImageMagick;
	// redirect std error to where std input goes, which is nowhere
	// see http://www.unix.org.ua/orelly/unix/upt/ch45_21.htm
	$output = shell_exec('which convert 2>&0');
	$path_to_convert = ($output !== NULL) ? trim($output) : FALSE;
	
} catch (Exception $e) {
	die($e->getMessage() . '<p>Please refer to <a href="http://omeka.org/codex/">Omeka documentation</a> for help.</p>');
}



$display_form = true;

//Try to actually install the thing
if (isset($_REQUEST['install_submit'])) {
	
	try {		
		$length_validator = new Zend_Validate_StringLength(4, 30);
		
		$validation = array(
					'administrator_email' => "EmailAddress",
					'thumbnail_constraint' => "Digits",
					'fullsize_constraint' => "Digits",
					'square_thumbnail_constraint' => "Digits",
					'username' => array('Alnum', $length_validator),		//At least 4 characters, _ . alphanumeric allowed
					'password' => $length_validator);				//At least 4 characters (all allowed)
		
		$filter = new Zend_Filter_Input(null, $validation, $_POST);
		
		//We got some errors
		if($filter->hasInvalid()) {
			$wrong = $filter->getInvalid();
			
			$msg = '';
			
			foreach ($wrong as $field => $m) {
				$explanation = array_pop($m);
				$msg .= "$field: $explanation.\n";
			}
			throw new Exception( $msg );
		}
		
		// Create the default user
		require_once 'User.php';
		require_once 'Person.php';
		
		$userTable = $db->User;
		$entityTable = $db->Entity;
		
		$entitySql = "INSERT INTO $entityTable (type, email, first_name, last_name) VALUES (?, ?, ?, ?)";
		$db->exec($entitySql, array("Person", $_POST['super_email'], 'Super', 'User'));
		
		$userSql = "INSERT INTO $userTable (username, password, active, role, entity_id) VALUES (?, SHA1(?), 1, 'super', LAST_INSERT_ID())";
		$db->exec($userSql, array($_POST['username'], $_POST['password']));
		
	
		// Namespace for the authentication session (to prevent clashes on shared servers)
		$optionTable = $db->Option;
		
		$optionSql = "INSERT INTO $optionTable (name, value) VALUES (?,?)";
		$db->exec($optionSql, array('auth_prefix', md5(mt_rand())));
		$db->exec($optionSql, array('migration', OMEKA_MIGRATION));
		
		// Add the settings to the db
		$settings = array('administrator_email', 'copyright', 'site_title', 'author', 'description', 'thumbnail_constraint', 'square_thumbnail_constraint', 'fullsize_constraint', 'path_to_convert');
		foreach ($settings as $v) {
			$db->exec($optionSql, array($v, $_POST[$v]));
		}
		
		$db->exec($optionSql, array('admin_theme', 'default'));
		$db->exec($optionSql, array('public_theme', 'default'));

		echo '<div id="intro">';
		echo '<h1>All Finished!</h1>';
		echo '<p>Omeka&#8217;s database is setup and you are ready to roll. <a href="'.dirname($_SERVER['REQUEST_URI']).'">Check out your site!</a></p>';
		echo '</div>';
		$display_form = false;

	} catch(Exception $e) {
		$error = $e->getMessage();
//		echo $e->getTraceAsString();
		$display_form = true;
	}
} 
?>

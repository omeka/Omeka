<?php 
try {
	require_once 'Zend/Config/Ini.php';
	$db_file = CONFIG_DIR . DIRECTORY_SEPARATOR . 'db.ini';
	if (!file_exists($db_file)) {
		throw new Zend_Config_Exception('Your Omeka database configuration file is missing.');
	}
	if (!is_readable($db_file)) {
		throw new Zend_Config_Exception('Your Omeka database configuration file cannot be read by the application.');
	}


	$db = new Zend_Config_Ini($db_file, 'database');
	Zend_Registry::set('db_ini', $db);

	//Fail on improperly configured db.ini file
	if (!isset($db->host) or ($db->host == 'XXXXXXX')) {
		throw new Zend_Config_Exception('Your Omeka database configuration file has not been set up properly.  Please edit the configuration and reload this page.');
	}

	$dsn = 'mysql:host='.$db->host.';dbname='.$db->name;
	if(isset($db->port)) {
		$dsn .= "port=" . $db->port;
	}
	
		$dbh = Zend_Db::factory('Mysqli', array(
	    'host'     => $db->host,
	    'username' => $db->username,
	    'password' => $db->password,
	    'dbname'   => $db->name
		));
} 
catch (Zend_Db_Adapter_Exception $e) {
    // perhaps a failed login credential, or perhaps the RDBMS is not running
	echo $e->getMessage();exit;
} 
catch (Zend_Exception $e) {
    // perhaps factory() failed to load the specified Adapter class
	echo $e->getMessage();exit;
}
catch (Zend_Config_Exception $e) {
	echo $e->getMessage();exit;
}
catch (Exception $e) {
	install_notification();
}

$db_obj = new Omeka_Db($dbh, $db->prefix);


Zend_Registry::set('db', $db_obj);
 
?>

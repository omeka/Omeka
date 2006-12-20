<?php
/**
 * Bootstrap
 */

/*
require_once "library/Doctrine/Doctrine.compiled.php";

// autoloading objects

function __autoload($class) {
    Doctrine::autoload($class);
}


// registering an autoload function, useful when multiple
// frameworks are using __autoload()

spl_autoload_register(array('Doctrine', 'autoload'));

*/

/*

// we may use PDO / PEAR like DSN
// here we use PEAR like DSN
//$dbh = new Doctrine_Db('mysql://root:@localhost/doctrine');
// !! no actual database connection yet !!
 
// initalize a new Doctrine_Connection
$manager = Doctrine_Manager::getInstance();

$conn = $manager->openConnection(new PDO("mysql:dbname=doctrine;host=localhost", "root", ""), "conn1");
*/

$t1 = microtime(true);
define('KEA_ROOT', dirname(__FILE__));
require_once 'app/config/env.php';
require_once 'Kea/Controller/Front.php';
echo Kea_Controller_Front::run();
echo (microtime(true) - $t1);
?>
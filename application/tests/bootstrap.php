<?php

ini_set('max_execution_time', 900);
ini_set('memory_limit', '32M');

// Load this while the include path contains the path to PEAR (which has
// conflicts because of naming clashes).
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/Constraint.php';

// Set the include path and all the constants.
require_once '../../paths.php';

// Restore the include path to use PEAR modules.
restore_include_path();
set_include_path(LIB_DIR . PATH_SEPARATOR . MODEL_DIR . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

require_once 'globals.php';

define('TEST_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'tests');
define('TEST_ASSETS_DIR', TEST_DIR .DIRECTORY_SEPARATOR . 'assets');

require_once 'Omeka/Core.php';

//Test bootstrap should automatically initialize the autoloader.
$core = new Omeka_Core;
$core->initializeClassLoader();

function setup_test_config()
{
    //Config dependency
    $config = new Zend_Config_Ini(APP_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR.'config.ini', 'testing');
    Omeka_Context::getInstance()->setConfig('testing', $config);
}

function setup_live_db()
{
    $config = Omeka_Context::getInstance()->getConfig('testing');
    
    $dbh = Zend_Db::factory('Mysqli', array(
        'host'     => $config->db->host,
        'username' => $config->db->username,
        'password' => $config->db->password,
        'dbname'   => $config->db->name
    	));

    $dbObj = new Omeka_Db($dbh);
    
    Omeka_Context::getInstance()->setDb($dbObj);
    
    //Register the original DB object as 'live_db' in case test cases want to use it
    Zend_Registry::set('live_db', $dbObj);
    
    return $dbObj;
}

function setup_test_acl()
{
    Mock::generate('Omeka_Acl');
    
    //Acl dependency
    $acl = new MockOmeka_Acl;

    //For testing purposes, all permissions checks should be OK'ed
    $acl->setReturnValue('checkUserPermission', true);
    
    Omeka_Context::getInstance()->setAcl($acl);
}

function setup_test_user($core)
{
    //logged-in user dependency
    $user = new stdClass;
    $user->id = 1;
    $user->username = "foobar";
    $user->first_name = "Foo";
    $user->last_name = "Bar";
    $user->role = "super";

    $core->setCurrentUser($user);
}
<?php 
ini_set('max_execution_time', 900);
ini_set('memory_limit', '32M');

require_once '../../paths.php';
require_once 'globals.php';

define('TEST_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'tests');
define('TEST_ASSETS_DIR', TEST_DIR .DIRECTORY_SEPARATOR . 'assets');

//Simpletest includes
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/mock_objects.php';
require_once 'simpletest/web_tester.php';

require_once 'IdenticalSqlExpectation.php';

require_once 'Omeka/Core.php';

//Test bootstrap should automatically filter magic quotes and initialize the 
//autoloader.
$core = new Omeka_Core;
$core->sanitizeMagicQuotes();
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

function setup_test_plugin_broker()
{
    require_once 'mocks.php';
    $broker = new Mock_Plugin_Broker;
    Omeka_Context::getInstance()->setPluginBroker($broker);
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

//Class definitions for mock objects
require_once 'mocks.php';

require_once 'OmekaTestCase.php';
//require_once 'OmekaControllerTestCase.php';

require_once 'Omeka/Record.php';
require_once 'Item.php';

require_once 'OmekaRecordTestCase.php';
require_once 'OmekaDbTestCase.php';
require_once 'MiscellaneousTestCase.php';	
require_once 'AclTestCase.php';
require_once 'ViewHelpersTestCase.php';

$test = new TestSuite('Omeka Tests');


//$test->addTestCase(new OmekaRecordTestCase());
$test->addTestCase(new OmekaDbTestCase());
$test->addTestCase(new MiscellaneousTestCase());
$test->addTestCase(new AclTestCase());
$test->addTestCase(new ViewHelpersTestCase());

$test->run(new HtmlReporter());
?>

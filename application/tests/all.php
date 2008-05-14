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
$core = new Omeka_Core;

$core->sanitizeMagicQuotes();
$core->initializeClassLoader();

function setup_test_config($core)
{
    //Config dependency
    $config = new Zend_Config_Ini(APP_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR.'config.ini', 'testing');
    $core->setConfig('testing', $config);
}

function setup_test_db($core)
{
    $config = $core->getConfig('testing');
    
    $dbh = Zend_Db::factory('Mysqli', array(
        'host'     => $config->db->host,
        'username' => $config->db->username,
        'password' => $config->db->password,
        'dbname'   => $config->db->name
    	));

    $dbObj = new Omeka_Db($dbh);
    
    $core->setDb($dbObj);
    
    //Register the original DB object as 'live_db' in case test cases want to use it
    Zend_Registry::set('live_db', $dbObj);
}

function setup_test_acl($core)
{
    Mock::generate('Omeka_Acl');
}

function setup_test_plugin_broker($core)
{
    
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

require_once 'TagTestCase.php';

require_once 'TaggableTestCase.php';
require_once 'ItemTestCase.php';
require_once 'ExhibitSectionTestCase.php';
require_once 'OmekaRecordTestCase.php';
require_once 'TypeTestCase.php';
require_once 'UploadTestCase.php';
require_once 'CollectionTestCase.php';
require_once 'UserTestCase.php';
require_once 'OmekaDbTestCase.php';
require_once 'FileMetadataTestCase.php';
require_once 'MiscellaneousTestCase.php';	
//require_once 'controllers/ExhibitsControllerTestCase.php';
require_once 'AclTestCase.php';
require_once 'ViewHelpersTestCase.php';

$test = new TestSuite('Omeka Tests');

/*$test->addTestCase(new TagTestCase());

$test->addTestCase(new ItemTestCase());
$test->addTestCase(new TaggableTestCase());
$test->addTestCase(new ExhibitSectionTestCase());	
$test->addTestCase(new OmekaRecordTestCase());
$test->addTestCase(new TypeTestCase());
$test->addTestCase(new CollectionTestCase());
$test->addTestCase(new UserTestCase());
$test->addTestCase(new OmekaDbTestCase());
$test->addTestCase(new FileMetadataTestCase());
//$test->addTestCase(new ExhibitsControllerTestCase());


//DO NOT RUN THIS ON A PRODUCTION INSTALLATION
$test->addTestCase(new UploadTestCase()); */
$test->addTestCase(new MiscellaneousTestCase());
$test->addTestCase(new AclTestCase());
$test->addTestCase(new ViewHelpersTestCase());

$test->run(new HtmlReporter());
?>

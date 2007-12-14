<?php 
ini_set('max_execution_time', 900);
ini_set('memory_limit', '32M');

require_once '../../paths.php';
require_once 'globals.php';
require_once 'plugins.php';

define('TEST_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'tests');
define('TEST_ASSETS_DIR', TEST_DIR .DIRECTORY_SEPARATOR . 'assets');

//Simpletest includes
require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/mock_objects.php';
require_once 'simpletest/web_tester.php';

require_once 'IdenticalSqlExpectation.php';

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

//Config dependency
$config = new Zend_Config_Ini(APP_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR.'config.ini', 'testing');
Zend_Registry::set('config_ini', $config);

//DB dependency
$dbh = Zend_Db::factory('Mysqli', array(
    'host'     => $config->db->host,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'dbname'   => $config->db->name
	));

//$dbh = new PDO('mysql:host=' . $config->db->host . ';dbname='.$config->db->name, $config->db->username, $config->db->password);
Zend_Registry::set('pdo', $dbh);

$db_obj = new Omeka_Db($dbh);

//Register the original DB object as 'live_db' in case test cases want to use it
Zend_Registry::set('live_db', $db_obj);
Zend_Registry::set('db', $db_obj);


Mock::generate('Omeka_Acl');
Mock::generate('PluginBroker', 'AbstractMockPluginBroker');


class MockPluginBroker extends AbstractMockPluginBroker
{
	private $hookCount = 0;
	
	public function expectHooks($hooks, $args=null)
	{
		foreach ($hooks as $key => $hook) {
			$hook_args = $args ? array($hook, $args) : array($hook);
			$this->expectAt($this->hookCount, '__call', $hook_args);
			$this->hookCount++;
		}
	}	
}

Mock::generate('Omeka_Db', 'AbstractMockOmeka_Db');

//Extend the mock DB class with convenience methods for checking SQL statements
class MockOmeka_Db extends AbstractMockOmeka_Db
{
	public function quote($text)
	{
		return "'" . $text . "'";
	}
	
	public function expectCountQuery($sql)
	{
		$this->expect(
					'fetchOne', 
					array(new IdenticalSqlExpectation($sql) ) );		
	}
	
	public function expectQuery($sql, $params=array())
	{
		$this->expectAtLeastOnce('query', 
			array(new IdenticalSqlExpectation($sql), $params) );
	}
	
	/**
	 * @param mixed bool|object
	 *
	 * @return void
	 **/
	public function setTable($record_class, $table_is_mock=true)
	{
		//Determine the class of the table to instantiate
		$table_class = $record_class . 'Table';
			
		if(!class_exists($table_class)) {
			$table_class = "Omeka_Table";
		}
		
		//We should set up a mock table
		if($table_is_mock === true) {
			Mock::generate($table_class);
			$mock_table_class = "Mock" . $table_class;
			
			$table = new $mock_table_class;
		}
		//We should set up an actual table instance
		elseif($table_is_mock === false) {
			$table = new $table_class($record_class);
		}
		//We are passed an actual object
		else {
			$table = $table_is_mock;
		}
		$this->setReturnValue('getTable', $table, array($record_class));
	}
}

//logged-in user dependency
$user = new stdClass;
$user->id = 1;
$user->username = "foobar";
$user->first_name = "Foo";
$user->last_name = "Bar";
$user->role = "super";

Zend_Registry::set('logged_in_user', $user);

require_once 'OmekaTestCase.php';
//require_once 'OmekaControllerTestCase.php';

require_once 'Omeka/Record.php';
require_once 'Item.php';

require_once 'TagTestCase.php';

require_once 'TaggableTestCase.php';
require_once 'ItemTestCase.php';
require_once 'ExhibitSectionTestCase.php';
require_once 'OmekaRecordTestCase.php';
require_once 'PermissionsTestCase.php';
require_once 'TypeTestCase.php';
require_once 'UploadTestCase.php';
require_once 'CollectionTestCase.php';
require_once 'UserTestCase.php';
require_once 'OmekaDbTestCase.php';
require_once 'FileMetadataTestCase.php';	
//require_once 'controllers/ExhibitsControllerTestCase.php';

$test = new TestSuite('Omeka Tests');

$test->addTestCase(new TagTestCase());

$test->addTestCase(new ItemTestCase());
$test->addTestCase(new TaggableTestCase());
$test->addTestCase(new ExhibitSectionTestCase());	
$test->addTestCase(new OmekaRecordTestCase());
$test->addTestCase(new PermissionsTestCase());
$test->addTestCase(new TypeTestCase());
$test->addTestCase(new CollectionTestCase());
$test->addTestCase(new UserTestCase());
$test->addTestCase(new OmekaDbTestCase());
$test->addTestCase(new FileMetadataTestCase());
//$test->addTestCase(new ExhibitsControllerTestCase());

//DO NOT RUN THIS ON A PRODUCTION INSTALLATION
$test->addTestCase(new UploadTestCase());

$test->run(new HtmlReporter());
?>

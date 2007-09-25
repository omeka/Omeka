<?php 

ini_set('max_execution_time', 900);

require_once '../../paths.php';

//set_include_path(get_include_path().PATH_SEPARATOR.APP_DIR.DIRECTORY_SEPARATOR.$site['simpletest']);

/*require_once 'Omeka/Logger.php';
$logger = new Omeka_Logger;
$logger->setSqlLog(dirname(__FILE__).DIRECTORY_SEPARATOR.'sql.log');
$logger->activateSqlLogging(true);	
*/
require_once 'Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));
$dbh = new PDO('mysql:host=localhost;dbname=omeka_test', 'root', '');

Doctrine_Manager::connection($dbh);

// sets a final attribute validation setting to true
$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine::ATTR_VLD, true);

Zend::register('doctrine', $manager);

require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/mock_objects.php';
require_once 'simpletest/web_tester.php';

require_once 'OmekaTestCase.php';

require_once 'Omeka/Record.php';
require_once 'Omeka/JoinRecord.php';
require_once 'Item.php';

require_once 'TagTestCase.php';
require_once 'TaggingsTestCase.php';
require_once 'TaggableTestCase.php';
require_once 'ItemTestCase.php';

$test = new TestSuite('Omeka Tests');

$test->addTestCase(new TagTestCase());
$test->addTestCase(new TaggingsTestCase());
$test->addTestCase(new TaggableTestCase());
$test->addTestCase(new ItemTestCase());

$test->run(new HtmlReporter());
?>

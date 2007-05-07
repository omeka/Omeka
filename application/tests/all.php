<?php 

ini_set('max_execution_time', 900);

require_once '../../paths.php';

//set_include_path(get_include_path().PATH_SEPARATOR.APP_DIR.DIRECTORY_SEPARATOR.$site['simpletest']);

/*require_once 'Kea/Logger.php';
$logger = new Kea_Logger;
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



require_once 'simpletest/unit_tester.php';
require_once 'simpletest/reporter.php';
require_once 'simpletest/mock_objects.php';

require_once 'OmekaTestCase.php';

require_once 'Kea'.DIRECTORY_SEPARATOR.'Record.php';
require_once 'Kea'.DIRECTORY_SEPARATOR.'JoinRecord.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Item.php';

//Mock::generate('Log');
require_once 'ItemTestCase.php';
require_once 'FormFunctionsTestCase.php';
require_once 'UserThemeFunctionsTestCase.php';
$test = new TestSuite('Omeka Tests');
$test->addTestCase(new ItemTestCase());
$test->addTestCase(new FormFunctionsTestCase());
$test->addTestCase(new UserThemeFunctionsTestCase());
$test->run(new HtmlReporter());
?>

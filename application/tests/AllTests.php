<?php 
require_once 'bootstrap.php';

require_once 'OmekaRecordTestCase.php';
require_once 'OmekaDbTestCase.php';
require_once 'MiscellaneousTestCase.php';	
require_once 'AclTestCase.php';
require_once 'ViewHelpersTestCase.php';

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();

        //$test->addTestCase(new OmekaRecordTestCase());
        $suite->addTestSuite('OmekaDbTestCase');
        $suite->addTestSuite('MiscellaneousTestCase');
        $suite->addTestSuite('AclTestCase');
        $suite->addTestSuite('ViewHelpersTestCase');
        
        return $suite;
    }
}


?>

<?php 
require_once 'bootstrap.php';
require_once 'OmekaControllerTestCase.php';
require_once 'OmekaRecordTestCase.php';
require_once 'OmekaDbTestCase.php';
require_once 'MiscellaneousTestCase.php';	
require_once 'AclTestCase.php';
require_once 'ViewHelpersTestCase.php';
require_once 'AdminThemeTest.php';
require_once 'ControllerAclTestCase.php';

class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();

        // $suite->addTestSuite('OmekaDbTestCase');
        // $suite->addTestSuite('MiscellaneousTestCase');
        // $suite->addTestSuite('AclTestCase');
        // $suite->addTestSuite('ViewHelpersTestCase');
        $suite->addTestSuite('ControllerAclTestCase');
        $suite->addTestSuite('AdminThemeTest');
        return $suite;
    }
}


?>

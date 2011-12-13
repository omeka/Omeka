<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Integration_TestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Drop all database tables prior to running integration tests.
     * 
     * Integration tests require the most up-to-date version of the database schema,
     * so tables should be dropped and rebuilt exactly once per test run.
     * 
     * Tables will be rebuilt by installer, which is run by all integration test
     * cases, i.e. all cases implementing Omeka_Test_AppTestCase.
     */
    public function setUp()
    {
        // The 'config' resource initializes and registers 'test_config'.
        $config = new Omeka_Test_Resource_Config;
        $config->init();
        $dbIni = Zend_Registry::get('test_config')->db;
        $helper = Omeka_Test_Helper_Db::factory($dbIni);
        $helper->dropTables($dbIni->prefix);
        $helper->getAdapter()->closeConnection();
    }

    public static function suite()
    {
        $suite = new Integration_TestSuite('Integration Tests');
        $facade = new File_Iterator_Facade;
        $suite->addTestFiles($facade->getFilesAsArray(
            dirname(__FILE__), array('Test.php')));
        return $suite;
    }
}


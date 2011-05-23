<?php
/**
 * @version $Id$
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
class PluginTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new PluginTests;
        $suite->addTest(self::_pluginTestSuite());
        return $suite;
    }
    
    /**
     * Check each plugin for tests and aggregate all plugin tests into a test
     * suite.
     * 
     * Check for an AllTests.php file in the plugin's tests/ directory.  If it
     * exists, assume that that is a test suite and try to load it.  Otherwise,
     * collect all the test files using the standard aggregator supplied by
     * PHPUnit.
     */
    private static function _pluginTestSuite()
    {
        $pluginDirIterator = new VersionedDirectoryIterator(PLUGIN_DIR);
        $suite = new PHPUnit_Framework_TestSuite('Plugin Tests');
        $pluginTestDirs = array();
        foreach ($pluginDirIterator as $pluginDir) {
            $pluginTestDir = PLUGIN_DIR . '/' . $pluginDir . '/' . 'tests'; 
            if (!is_dir($pluginTestDir)) {
                continue;
            }
            $suiteFile = $pluginTestDir . '/AllTests.php'; 
            if (is_file($suiteFile)) {
                $suite->addTestFile($suiteFile);
            } else {
                $pluginTestDirs[] = $pluginTestDir;
            }
        }
        
        if ($pluginTestDirs) {
            $testCollector = new PHPUnit_Runner_IncludePathTestCollector(
                $pluginTestDirs
            );
            $suite->addTestFiles($testCollector->collectTests());
        }
        return $suite;
    }        
}
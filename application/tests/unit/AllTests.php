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
class Unit_AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new Unit_AllTests('Unit Tests');
        $testCollector = new PHPUnit_Runner_IncludePathTestCollector(
          array(dirname(__FILE__))
        );
        $suite->addTestFiles($testCollector->collectTests());
        return $suite;
    }
}

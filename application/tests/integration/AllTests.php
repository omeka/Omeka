<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 */
class Integration_AllTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new Integration_AllTests('Integration Tests');
        $testCollector = new PHPUnit_Runner_IncludePathTestCollector(
          array('integration')
        );
        $suite->addTestFiles($testCollector->collectTests());
        return $suite;
    }
}
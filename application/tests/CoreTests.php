<?php
/**
 * @version $Id$
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

require_once dirname(__FILE__) . '/integration/AllTests.php';
require_once dirname(__FILE__) . '/unit/AllTests.php';

/**
 * 
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class CoreTests extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTest(Integration_AllTests::suite());
        $suite->addTest(Unit_AllTests::suite());
        return $suite;
    }    
}

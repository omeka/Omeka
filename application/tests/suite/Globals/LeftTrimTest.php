<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Test set_theme_option().
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Globals_LeftTrimTest extends PHPUnit_Framework_TestCase
{
    public function testHasTrimString()
    {
        $beforeString = '/Bob/Fred';
        $afterString = '/Fred';
        $this->assertEquals($afterString, left_trim($beforeString, '/Bob'));
    }
    
    public function testResultEmptyString()
    {
        $beforeString = '/Fred';
        $afterString = '';
        $this->assertEquals($afterString, left_trim($beforeString, '/Fred'));
    }
    
    public function testLacksTrimString()
    {
        $beforeString = '/Bob/Fred';
        $afterString = '/Bob/Fred';
        $this->assertEquals($afterString, left_trim($beforeString, '/Sally'));
    }
    
    public function testNotRightTrimString()
    {
        $beforeString = '/Bob/Fred';
        $afterString = '/Bob/Fred';
        $this->assertEquals($afterString, left_trim($beforeString, '/Fred'));
    }
}

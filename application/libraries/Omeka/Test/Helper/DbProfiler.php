<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Catch-all class for database helper methods that are shared across test cases.
 * 
 * @package Omeka\Test\Helper
 */
class Omeka_Test_Helper_DbProfiler
{
    /**
     * @var Zend_Db_Profiler
     */
    private $_profiler;
    
    /**
     * @var PHPUnit_Framework_TestCase
     */
    private $_test;
    
    /**
     * Constructor.
     * 
     * @param Zend_Db_Profiler $profiler
     * @param PHPUnit_Framework_Assert $test
     */
    public function __construct(Zend_Db_Profiler $profiler, PHPUnit_Framework_Assert $test)
    {
        $this->_profiler = $profiler;
        $this->_test = $test;
    }
    
    public function assertDbQuery($sqlPart, $message = null)
    {
        $queryProfiles = $this->_profiler->getQueryProfiles();
        $this->_test->assertTrue(is_array($queryProfiles), "No database queries were made.");
        $ranQuery = false;
        if (is_array($sqlPart)) {
            $query = $sqlPart[0];
            $params = $sqlPart[1];
        } else {
            $query = $sqlPart;
        }

        foreach ($queryProfiles as $profile) {            
            if (strpos($profile->getQuery(), $query) !== false) {
                if (isset($params) && $profile->getQueryParams() == $params) {
                    $ranQuery = true;
                    break;
                } else if (!isset($params)) {
                    $ranQuery = true;
                    break;
                }
            }
        }
        $this->_test->assertTrue($ranQuery, $message . PHP_EOL . "Should have run SQL query containing '$query'." 
            . (isset($params) ? PHP_EOL . "Should have been passed parameters: " . print_r($params, true) : ''));
    }
    
    /**
     * Assert that the given number of SQL queries were made.
     * 
     * @param integer $queryCount
     */
    public function assertTotalNumQueries($queryCount, $msg = null)
    {
        if (!$msg) {
            $msg = "Failed asserting that " . (integer)$queryCount . " SQL queries were made.";
        }
        $this->_test->assertEquals($queryCount, $this->_profiler->getTotalNumQueries(),
            $msg);
    }
}

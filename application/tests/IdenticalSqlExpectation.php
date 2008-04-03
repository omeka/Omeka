<?php 
/**
 *    Test for a pattern using Perl regex rules.
 *    @package SimpleTest
 *    @subpackage UnitTester
 */
class IdenticalSqlExpectation extends SimpleExpectation {
	
    /**
     *    Sets the value to compare against.
     *    @param string $pattern    Pattern to search for.
     *    @param string $message    Customised message on failure.
     *    @access public
     */
    function IdenticalSqlExpectation($sql, $message = '%s') {
        $this->SimpleExpectation($message);
        $this->_sql = $sql;
    }

    /**
     *    Tests the expectation. True if the Perl regex
     *    matches the comparison value.
     *    @param string $compare        Comparison value.
     *    @return boolean               True if correct.
     *    @access public
     */
    function test($compare) {
		$input = $this->strip($compare);
		$test = $this->strip($this->_sql);
//Zend_Debug::dump( $input );Zend_Debug::dump( $test );exit;
        return (strcmp($input, $test) == 0);
    }
	
	function strip($text) {
		$stripped = trim(str_replace(array("\r", "\t"), '', $text));
		// Newlines must be replaced by spaces, double spaces must be replaced by single space
		$more_stripped = str_replace('  ', ' ', str_replace("\n", ' ', $stripped));
		return $more_stripped;
	}
    /**
     *    Returns a human readable test message.
     *    @param mixed $compare      Comparison value.
     *    @return string             Description of success
     *                               or failure.
     *    @access public
     */
    function testMessage($compare) {
        if (!$this->test($compare)) {
             $dumper = &$this->_getDumper();
            return "SQL statement [" . $this->_sql .
                    "] is not the same as [" .
                    $compare . "]";     
		}   
    }
} 
?>

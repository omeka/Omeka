<?php

/**
 * @version 0.1.0
 * @edited 5/8/06
 * @status FROZEN
 */
class Kea_Exception extends Exception
{
	public function __toString()
	{
		$string = 'Error: ';
		switch( KEA_DEBUG_ERRORS ) {
			case( 1 ):
				// E_STRICT
			case( 2 ):
				// E_ALL
				$string .= 'Trace: ' . $this->getTraceAsString() . "\n";
				$string .= 'In file: ' . $this->getFile() . "\n";
				$string .= 'On line: ' . $this->getLine() . "\n";
			case( 3 ):
				// E_ALL ^ E_NOTICE
			case( 4 ):
				// E_WARNING
				$string .= $this->getMessage();
			case( 5 ):
				// Error reporting turned off
			default:
			break;
		}
		return $string;
	}
}
?>
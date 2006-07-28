<?php

class Kea_Logger
{
	public static function logSQL( $message )
	{
		$final = '========================' . "\n";
		$final .= 'Type: SQL' . "\n";
		$final .= 'Date: ' . date( DATE_ISO8601, time() ) . "\n";
		$final .= $message . "\n";
		$final .= '========================' . "\n";
		file_put_contents( KEA_SQL_LOG, $final, FILE_APPEND );
	}
}

?>
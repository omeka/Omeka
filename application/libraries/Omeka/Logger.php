<?php 
/**
 * undocumented class
 *
 * @package Omeka
 * @author CHNM
 **/
class Omeka_Logger
{
	private static $sqlLog;
	private static $errorLog;
	
	private static $logSql = false;
	private static $logErrors = false;
		
	private static $totalQueryTime = 0;
	private static $totalQueries = 0;
	
	public static function activateSqlLogging($bool=true) {
		self::$logSql = $bool;
	}
	
	public static function activateErrorLogging($bool=true) {
		self::$logErrors = $bool;
	}
	
	public function setQueryStart() {
		self::$lastSqlQueryTime = microtime(true);
	}
	public static function setSqlLog($path) {
		if(!is_writable($path)) throw new Exception( 'Sql log file cannot be written to.  Please give this file read/write permissions for the web server.' );
		self::$sqlLog = $path;
	}
	
	public function setErrorLog($path) {
		if(!is_writable($path)) throw new Exception( 'Error log file cannot be written to.  Please give this file read/write permissions for the web server.' );
		self::$errorLog = $path;
	}
	
	public static function logSql( $sql, $params = array() )
	{
		if(self::$logSql) {
			self::$totalQueryTime += $execTime;
			self::$totalQueries++;
			$final = '========================' . "\n";		
			$final .= 'Type: SQL' . "\n";
			$final .= 'Date: ' . date( DATE_ISO8601, time() ) . "\n";
			$final .= $sql . "\n";
			if(!empty($params)) {
				$final .= 'Parameters: ' . print_r($params, true) . "\n";
			}
			$final .= 'Execution time: '.$execTime. "\n";
			$final .= '========================' . "\n";
			file_put_contents( self::$sqlLog, $final, FILE_APPEND );
		}
	}
	
	public static function logError( Exception $e )
	{
		if($e) {
			$final  = '=======================' . "\n";
			$final .= 'Type: '.get_class($e)."\n";
			$final .= 'Date: ' . date( DATE_ISO8601, time() ) . "\n";
			$final .= $e->getMessage()."\n";
			$final .= $e->getFile().':'.$e->getLine()."\n";
			$final .= $e->getTraceAsString()."\n";
			$final .= '=======================' . "\n";
			file_put_contents( self::$errorLog, $final, FILE_APPEND );
		}
	}
	
	public static function logQueryTotal() 
	{
		$final  = '=======================' . "\n";
		$final .= 'Total Query Execution Time: '.self::$totalQueryTime ."\n";
		$final .= 'Total # of Queries: '.self::$totalQueries."\n";
		$final .= '========================' . "\n";
		file_put_contents( self::$sqlLog, $final, FILE_APPEND );
	}
}
 // END class Omeka_Log 
?>

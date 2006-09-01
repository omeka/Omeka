<?php

class Kea_DB_Connection
{	
	private static $_instance;
	private static $_db;
	
	/**
	 * Singleton
	 */
	private function __construct() {}
	private function __clone() {}
	
	public static function instance()
	{
		if( !self::$_instance instanceof self )
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function connect( array $connect = null )
	{
		if( $connect )
		{
			$host		= $connect['host'];
			$username	= $connect['username'];
			$password	= $connect['password'];
			$dbname		= null;
			$port		= $connect['port'];
			$socket		= $conncet['socket'];
		}
		else
		{
			$host		= KEA_MYSQLI_HOST;
			$username	= KEA_MYSQLI_USERNAME;
			$password	= KEA_MYSQLI_PASSWORD;
			$dbname		= null;
			$port		= KEA_MYSQLI_PORT;
			$socket		= KEA_MYSQLI_SOCKET;
		}
		
		self::$_db = new mysqli( $host, $username, $password, $dbname, $port, $socket );
		
		if( mysqli_connect_errno() )
		{
			throw new Kea_DB_Connection_Exception(
				'Database connection error: ' . mysqli_connect_error() );
		}
		
		if( $connect )
		{
			$dbname = $connect['dbname'];
			$set = self::$_db->select_db(  );
		}
		else
		{
			$set = self::$_db->select_db( KEA_MYSQLI_DBNAME );
		}
		
		if( $set === false )
		{
			throw new Kea_DB_Connection_Exception(
				'Could not select database ' . KEA_MYSQLI_DBNAME );
		}
		return self::$_db;
	}
	
	public static function checkConnection()
	{
		if( self::$_db instanceof mysqli && self::$_db->ping() )
		{
			return true;
		}
		return false;
	}
	
	public function __destruct()
	{
		if( self::$_db->ping() )
		{
			self::$_db->close();
		}
	}
}

?>
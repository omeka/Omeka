<?php 
/**
* 
*/
class Omeka_Db_Exception extends Exception
{
	protected $e;
	
	public function __construct(Exception $e, $sql)
	{
		$this->e = $e;
		$this->sql = $sql;
		
		if(Zend_Registry::isRegistered('config_ini')) {
			$config = Zend_Registry::get('config_ini');
		}
		
				
		if(!$config or !$config->debug->exceptions) {
			$this->message = "An error has occurred within Omeka's database.  In order to see the extended explanation of this error, please enable debugging.  If you do not know how to do this, please refer to the Omeka documentation.";
		}
		else {
			ob_start();
			
			echo "An error occurred within the following SQL statement:\n\n" . (string) $sql . 
			"\n\n\rStack Trace:\n" . $e->getTraceAsString() .
			"\n\n\rDump of the exception:\n";
			var_dump( $e );
			$this->message = ob_get_clean();
		}
	}
	
	public function getInitialException()
	{
		return $this->e;
	}
}
 
?>

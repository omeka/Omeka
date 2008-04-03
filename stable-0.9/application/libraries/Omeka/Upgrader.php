<?php 
define('UPGRADE_DIR', APP_DIR.DIRECTORY_SEPARATOR.'migrations');
/**
 * This will be a wrapper for upgrade operations on Omeka.
 *
 * @package Omeka
 * @author CHNM
 **/
class Omeka_Upgrader
{
	protected $manager;
	protected $start;
	protected $end;
	protected $current;
	protected $errors = array();
	protected $output = array();
 	const VERSION_OPTION = 'migration';
	
	public function __construct($fromVersion, $toVersion)
	{
		ini_set('max_execution_time', 0);
		
		$this->db = get_db();
		$this->start = $fromVersion;
		$this->end = $toVersion;				
	}
	
	public function getErrors()
	{
	    return $this->errors;
	}
	
	private function emailAdministrator($error)
	{
        //If there was a problem with the upgrade, display the error message 
		//and email it to the administrator
		$email = get_option('administrator_email');
		
		$header = 'From: '.$email. "\n" . 'X-Mailer: PHP/' . phpversion();
		$title = "Omeka Upgrade Error";
		$body = "This error was thrown when attempting to upgrade your Omeka installation:\n\n" . $error;
		mail($email, $title, $body, $header);	    
	}
	
	public function run()
	{
    	for ($i = $this->start+1; $i < $this->end+1; $i++) { 
		
    		//Start capturing the output
    		ob_start();
    		try {
    			//Start the upgrade script
    			$this->upgrade($i);
    			
    			$this->output[$i] = "Successfully migrated #$i";
    		} catch (Omeka_Db_Exception $e) {
    			$db_exception = $e->getInitialException();
			
    			$error = "Error in Migration #$i" . "\n\n";
    			$error .= "Message: " . $db_exception->getMessage() . "\n\n"; 
    			$error .= "Code: " . $db_exception->getCode() . "\n\n";
    			$error .= "Line: " . $db_exception->getLine() . "\n\n";
								
    			$this->errors[$i] = $error;
		
    		} catch (Exception $e) {
    		    $error = "Error in Migration #$i\n\n";
    		    $error .= "Message: " . $e->getMessage() . "\n\n";
		    
    		    $this->errors[$i] = $error;
    		}
    		$this->current = $i;
    		$this->incrementMigration();
		
    		//Retrieve anything that an individual upgrade script may have output
    		$output = ob_get_clean();
    		if($output) {
    		    $this->output[$i] .= $output;
    		}
    	}			    
	}
	
	public function upgrade($version) {
		//We may need to have a form or something in case the upgrade requires user input
		$formPath = UPGRADE_DIR.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR.'form.php';
		
		if(empty($_POST) and file_exists($formPath)) {
			include $formPath;
			exit;
		}
		
		$scriptPath = UPGRADE_DIR.DIRECTORY_SEPARATOR.$version.DIRECTORY_SEPARATOR.'upgrade.php';
		if(!file_exists($scriptPath)) {
			throw new Exception( 'Migration does not have any scripts associated with it!' );
		}
		
		include $scriptPath;
	}

	public function incrementMigration() {
		require_once 'Option.php';
		$optTable = $this->db->Option;
		$this->db->exec("UPDATE $optTable SET value = {$this->current} WHERE name = '" . self::VERSION_OPTION . "'");
	}
	
	public function hasTable($model) {
		$tbl = $this->db->$model;		
		$res = $this->query("SHOW tables LIKE '$tbl'");
		return !empty($res);
	}
	
	public function tableHasColumn($model, $column) {
		$col = $this->getColumnDefinition($tblName, $column);
		return !empty($col);
	}
	
	public function getColumnDefinition($table, $column) {
		//Replace with SHOW COLUMNS		
		
		$tblName = $this->db->$table;
		
		$explain = $this->query("EXPLAIN `$tblName`");
		foreach ($explain as $k => $col) {
			if($column == $col['Field'] ) {
				return $col;
			}
		}
		return false;
	}
	
	public function getOutput()
	{
	    return $this->output;
	}
} // END class Omeka_Upgrader 
?>
<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * Handles database migrations for Omeka.
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/ 
class Omeka_Upgrader
{
	protected $start;
	protected $end;
	protected $current;
	protected $errors = array();
	protected $output = array();
 	
 	protected $versionOption = 'migration';
 	protected $migrationDir = UPGRADE_DIR;
	
	public function __construct($fromVersion, $toVersion)
	{
		ini_set('max_execution_time', 0);
		
		$this->db = Omeka_Context::getInstance()->getDb();
		$this->start = $fromVersion;
		$this->end = $toVersion;				
	}
	
	public function setVersionOption($name)
	{
	    $this->versionOption = $name;
	}
	
	public function setMigrationDirectory($dir)
	{
	    $this->migrationDir = $dir;
	}
	
	public function getErrors()
	{
	    return $this->errors;
	}
	
	private function downgrade()
	{
        $i = (int) $this->start;
        
        while ($i > (int) $this->end) {
            ob_start();
            
            $this->down($i);
            $this->output[$i][] = "Successfully downgraded #$i";
            //Retrieve anything that an individual upgrade script may have output
    		$output = ob_get_clean();
    		if ($output) {
    		    $this->output[$i][] = $output;
    		}
            
            $this->current = --$i;
            $this->setMigration($this->current);
        }	    
	}
	
	private function upgrade()
	{
	    $i = $this->start + 1;
	    
        while ($i <= (int) $this->end) { 

    		ob_start();
            
            //Set the current migration #, in case of error
            $this->current = $i;
            
            //Run the migration # $i
            $this->up($i);
            
            //This won't be reached if there was an error
            $this->output[$i][] = "Successfully migrated #$i";
            //Retrieve anything that an individual upgrade script may have output
    		$output = ob_get_clean();
    		if ($output) {
    		    $this->output[$i][] = $output;
    		}
            
            $i++;        
            
            //Set the migration # in the db to the pre-incremented value
            $this->setMigration($this->current);
            
            
        }	  
	}
	
	public function run()
	{
	    try {
	        $this->current = (int) $this->start;
    	    //Upgrade or downgrade depending on whether we started higher or lower
		    ($this->start > $this->end) ? $this->downgrade() : $this->upgrade();
	    } catch (Exception $e) {
	       $this->addMigrationError($e);
	    }
	}
	
	private function addMigrationError($e) {
	    $error = "Error in Migration #{$this->current}" . "\n\n";
	    
	    if ($e instanceof Zend_Db_Adapter_Exception) {
            $db_exception = $e->getChainedException();
            			
			$error .= "Message: " . $db_exception->getMessage() . "\n\n"; 
			$error .= "Code: " . $db_exception->getCode() . "\n\n";
			$error .= "Line: " . $db_exception->getLine() . "\n\n";
	    } else {
		    $error .= "Message: " . $e->getMessage() . "\n\n";	    
	    }
	    
	    $this->errors[$this->current] = $error;	    
	}
	
	private function down($version) {
	    $class = $this->getMigrationClass($version);
	    $migration = new $class($this->db);	    
	    
	    if (!method_exists($migration, 'down')) {
	        throw new Exception("Migration #$version is irreversible (there is no down() method)!");
	    }
	    
	    $migration->down($version);
	}
	
	/**
	 * 
	 * @param string
	 * @return void
	 **/
	private function getMigrationClass($version) {
	    $f = glob(sprintf($this->migrationDir . DIRECTORY_SEPARATOR. '%03d_*.php', $version));
        
        $path = current($f);
        
        if (!file_exists($path)) {
            throw new Exception("Migration file does not exist for #$version!");
        }
        
	    //Include the file
	    require_once $path;
	    
	    //Match the class name portion of the file name
	    $filename = basename($path, '.php');
	    
	    if (!preg_match('/^\d{3}_(\w+)$/', $filename, $match)) {
	        throw new Exception('Migration file does not follow proper naming conventions!');
	    }
	    
	    $class = ucfirst($match[1]);
	    
	    if (!class_exists($class)) {
	        throw new Exception('Migration file named "' . $filename . '" does not contain a class named "' . $class . '"!');
	    }
	    
	    return $class;	    
	}
	
	private function up($version) {
		$class = $this->getMigrationClass($version);
		$migration = new $class($this->db);
		
		//Migrations can output HTML for a form that will then be processed by that migration
		if (empty($_POST) and ($output = $migration->form())) {
			echo $output;
			exit;
		}
		
		$migration->up();
	}

	private function setMigration($num) {
	    $num = (int) $num;
	    
		require_once 'Option.php';
		$optTable = $this->db->Option;
		$this->db->exec("UPDATE $optTable SET value = $num WHERE name = '" . $this->versionOption . "'");
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
			if ($column == $col['Field'] ) {
				return $col;
			}
		}
		return false;
	}
	
	public function getOutput()
	{
	    return $this->output;
	}
}
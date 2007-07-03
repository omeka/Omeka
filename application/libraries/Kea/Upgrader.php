<?php 
define('UPGRADE_DIR', APP_DIR.DIRECTORY_SEPARATOR.'migrations');
/**
 * This will be a wrapper for upgrade operations on Omeka.
 *
 * @package Omeka
 * @author CHNM
 **/
class Kea_Upgrader
{
	protected $manager;
	protected $start;
	protected $end;
	protected $current;
 	const VERSION_OPTION = 'migration';
	
	public function __construct($manager, $fromVersion, $toVersion)
	{
		$this->manager = $manager;
		$this->start = $fromVersion;
		$this->end = $toVersion;
		
		for ($i = $fromVersion+1; $i < $toVersion+1; $i++) { 
			$this->upgrade($i);
			$this->current = $i;
			$this->incrementMigration();
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
	
	public function getTable($model) {
		require_once $model.'.php';
		return $this->manager->getTable($model);
	}
	
	public function getTableName($model) {
		require_once $model . '.php';
		return $this->manager->getTable($model)->getTableName();
	}
	
	public function incrementMigration() {
		require_once 'Option.php';
		$optTable = $this->getTableName('Option');
		$this->query("UPDATE $optTable SET value = {$this->current} WHERE name = '".self::VERSION_OPTION. "'");
	}
	
	public function hasTable($tbl) {
		$res = $this->query("SHOW tables LIKE '$tbl'");
		return !empty($res);
	}
	
	public function tableHasColumn($model, $column) {
		$col = $this->getColumnDefinition($model, $column);
		return !empty($col);
	}
	
	public function getColumnDefinition($model, $column) {
		//Replace with SHOW COLUMNS
		
		$tblName = $this->getTableName($model);
		$explain = $this->query("EXPLAIN `$tblName`");
		foreach ($explain as $k => $col) {
			if($column == $col['Field'] ) {
				return $col;
			}
		}
		return false;
	}
	
	public function query($sql) {
		//Echo each query as it is run
		echo $sql;
		
		$conn = $this->manager->connection();
		
		$res = $conn->execute($sql);
		return $res->fetchAll();
	}
	
} // END class Kea_Upgrader 
?>

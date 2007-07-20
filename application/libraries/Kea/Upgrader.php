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
		$this->manager->setAttribute(Doctrine::ATTR_CREATE_TABLES, false);
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
		$file = $model . '.php';

		if(!file_exists(MODEL_DIR.DIRECTORY_SEPARATOR.$file)) {
			throw new Exception( 'This model does not exist' );
		}
		require_once $model . '.php';
		return $this->manager->getTable($model)->getTableName();
	}
	
	public function buildTable($model) {
		require_once $model.'.php';
		return $this->manager->getTable($model)->export();
	}
	
	public function incrementMigration() {
		require_once 'Option.php';
		$optTable = $this->getTableName('Option');
		$this->query("UPDATE $optTable SET value = {$this->current} WHERE name = '".self::VERSION_OPTION. "'");
	}
	
	public function hasTable($model) {
		try {
			$tbl = $this->getTableName($model);
		} catch (Exception $e) {
			$tbl = $model;
		}
				
		$res = $this->query("SHOW tables LIKE '$tbl'");
		return !empty($res);
	}
	
	public function tableHasColumn($model, $column) {
		//If it is a model and not a table name, 
		$file = MODEL_DIR.DIRECTORY_SEPARATOR.$model.'.php';
		if(file_exists($file)) {
			require_once $file;
			$tblName = $this->getTableName($model);
		}else {
			$tblName = $model;
		}
		$col = $this->getColumnDefinition($tblName, $column);
		return !empty($col);
	}
	
	public function getColumnDefinition($tblName, $column) {
		//Replace with SHOW COLUMNS		
		$explain = $this->query("EXPLAIN `$tblName`");
		foreach ($explain as $k => $col) {
			if($column == $col['Field'] ) {
				return $col;
			}
		}
		return false;
	}
	
	public function query($sql, $params=array()) {
		//Echo each query as it is run
		Zend::dump( $sql );
		
		$conn = $this->manager->connection();
		
		$res = $conn->execute($sql, $params);
		return $res->fetchAll(PDO::FETCH_ASSOC);
	}
	
} // END class Kea_Upgrader 
?>

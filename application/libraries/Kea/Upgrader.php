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
		
		//Display a nice omeka header for the upgrade tool
		$this->displayHeader();
		
		for ($i = $fromVersion+1; $i < $toVersion+1; $i++) { 
			
			//Start capturing the output
			ob_start();
			try {
				//Start the upgrade script
				$this->upgrade($i);
			} catch (Exception $e) {
				
				$error = "Error in Migration #$i" . "\n\n";
				$error .= "Message: " . $e->getMessage() . "\n\n"; 
				$error .= "Code: " . $e->getCode() . "\n\n";
				$error .= "Line: " . $e->getLine() . "\n\n";
				$error .= "Output from upgrade: ". ob_get_contents();
				
				//If there was a problem with the upgrade, display the error message 
				//and email it to the administrator
				$email = get_option('administrator_email');
				
				$header = 'From: '.$email. "\n" . 'X-Mailer: PHP/' . phpversion();
				$title = "Omeka Upgrade Error";
				$body = "This error was thrown when attempting to upgrade your Omeka installation:\n\n" . $error;
				mail($email, $title, $body, $header);
				$this->displayError($error);
				
				
			}
			$this->current = $i;
			$this->incrementMigration();
			
			//Clean the contents of the output buffer
			ob_end_clean();
			
			if(!isset($error)) {
				$this->displaySuccess();
			}
			
			unset($error);
			
			
		}
		
		$this->displayFooter();
	}
	
	public function displayError($text) {
?>
	<p class="error">Omeka encountered an error when upgrading your installation.  The full text of this error has been emailed to your administrator:</p>
	
	<p class="error_text"><?php echo htmlentities($text); ?></p>
<?php 		
	}
	
	public function displaySuccess() {
		?>
		<p class="success">Successfully migrated #<?php echo $this->current; ?></p>
<?php		
	}
		
	public function displayHeader() {
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Upgrading Omeka</title>
<style type="text/css" media="screen">
	body {
		font: arial, sans-serif;
	}
	
</style>
</head>

<body>
	<h2 class="instruction">Omeka is now upgrading itself.  Please refresh your screen once this page finishes loading.</h2>	
<?php		
	}
	
	public function displayFooter() {
		?>
	</body>	
<?php		
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
	
	public function query($sql, $params=array(), $fetchOne=false) {
		//Echo each query as it is run
		Zend::dump( $sql );
		
		$conn = $this->manager->connection();
		
		if($fetchOne)
			return $conn->fetchOne($sql, $params);
			
		$res = $conn->execute($sql, $params);
		if ($res->columnCount() != 0)
			return $res->fetchAll(PDO::FETCH_ASSOC);
		else
			return array();
	}
	
} // END class Kea_Upgrader 
?>
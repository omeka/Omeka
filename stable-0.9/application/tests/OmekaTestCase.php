<?php 
/**
 * undocumented class
 *
 * @package Omeka
 * @author CHNM
 **/
abstract class OmekaTestCase extends UnitTestCase
{	
	public function setUp() {
		include 'dependencies.php';		
    }
	
	/**
	 * Truncate all the tables in the test DB, when given an actual database object
	 *
	 * DON'T YOU EVER EVEN THINK OF THINKING ABOUT RUNNING THIS ON A PRODUCTION DATABASE.  I KILL YOU.
	 *
	 * @return void
	 **/
	protected function cleanDb(Omeka_Db $db)
	{
		$tables = $db->fetchCol("SHOW TABLES");
			
		if(empty($tables)) {
			// Build the tables explicitly
			include BASE_DIR . '/install/install.sql.php';
			$db->execBlock($install_sql);
		}

		
		foreach ($tables as $table) {
			$db->exec("TRUNCATE `$table`");
		}
	}
	
	/**
	 * Use this to set the current DB object to a mock or actual DB for testing
	 *
	 * @return void
	 **/
	protected function setDb($db)
	{
		Zend_Registry::set('db', $db);
	}
	
	protected function getLiveDb()
	{
		return Zend_Registry::get('live_db');
	}
	
	protected function setUpLiveDb()
	{
		$db = $this->getLiveDb();
		
		$this->setDb($db);
		
		$this->cleanDb($db);
	}
		
	public function init() {}
	
	public function getTable($name) { return $this->db->getTable($name);}
	
	protected function stripSpace($html)
	{
		return str_replace(array("\t","\n"),'',$html);
	}
	
} // END abstract class OmekaTestCase extends UnitTestCase 
?>

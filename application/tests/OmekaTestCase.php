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
		setup_test_acl();
        setup_test_plugin_broker();

        //Database connection dependency
        $db = new MockOmeka_Db;

        //All queries should return a PDO Statement object unless told otherwise
        $stmt = new PDOStatement;
        $db->setReturnValue('query', $stmt);

        Zend_Registry::set('db', $db);
        $this->db = $db;
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
		
	protected function setUpLiveDb()
	{
	    setup_test_config();
		$db = setup_live_db();
		
		$this->cleanDb($db);
		
		$this->db = $db;
	}
		
	public function init() {}
	
	public function getTable($name) { return $this->db->getTable($name);}
	
	protected function stripSpace($html)
	{
		return str_replace(array("\t","\n"),'',$html);
	}
	
} // END abstract class OmekaTestCase extends UnitTestCase 
?>

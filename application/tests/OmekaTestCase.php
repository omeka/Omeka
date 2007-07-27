<?php 
/**
 * undocumented class
 *
 * @package Omeka
 * @author CHNM
 **/
abstract class OmekaTestCase extends UnitTestCase
{
	protected $fixtures;
	private $init = false;
	
	public function setUp($pathToSetup=null) {
        if( ! $this->init) {
			$this->manager = Doctrine_Manager::getInstance();
			$this->wipeDb($pathToSetup);
			$this->setUpFixtures($pathToSetup);
			$this->init();
		}
        $this->init    = true;
    }
	public function wipeDb($pathToSetup) {
		
		$conn = $this->manager->connection();
		$conn->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
		
		//Get a list of tables existing and delete them
		$tables = $conn->execute("SHOW TABLES")->fetchAll();
		foreach ($tables as $table) {
			$sql = "DROP TABLE IF EXISTS `{$table[0]}`";
			$conn->execute($sql);
		}
		//Now reinstall that sheit
		$sql = file_get_contents(BASE_DIR.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'install.sql');
		$conn->execute($sql);
		
		//Now install the base SQL for the tests
		$pathToSetup = (!empty($pathToSetup)) ? $pathToSetup . DIRECTORY_SEPARATOR . 'setup.sql' : 'setup.sql';
 		$sql = file_get_contents($pathToSetup);
		$conn->execute($sql);
		
	}
	
	protected function setUpFixtures($path)
	{
		include $path . DIRECTORY_SEPARATOR. 'fixtures.php';
		$this->fixtures = $fixtures;
	}
	
	public function init() {}
	
	public function getTable($name) { return $this->manager->getTable($name);}
	
	protected function stripSpace($html)
	{
		return str_replace(array("\t","\n"),'',$html);
	}
	
} // END abstract class OmekaTestCase extends UnitTestCase 
?>

<?php 
/**
 * undocumented class
 *
 * @package Omeka
 * @author CHNM
 **/
abstract class OmekaTestCase extends UnitTestCase
{
	private $init = false;
	
	public function setUp() {
        if( ! $this->init) {
			$this->manager = Doctrine_Manager::getInstance();
			$this->wipeDb();
			$this->init();
		}
        $this->init    = true;
    }
	public function wipeDb() {
		$conn = $this->manager->connection();
		$sql = file_get_contents('fresh_db.sql');
		$conn->execute($sql);		
	}
	public function init() {}
} // END abstract class OmekaTestCase extends UnitTestCase 
?>

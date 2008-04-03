<?php 
Mock::generate('Zend_Db_Adapter_Mysqli');
/**
* 
*/
class OmekaDbTestCase extends OmekaTestCase
{
	protected $adapter;
	
	public function setUp()
	{
		$this->adapter = new MockZend_Db_Adapter_Mysqli;
	}
	
	public function testDbCanRetrieveTableNameWithPrefix()
	{
		$db_with_prefix = new Omeka_Db($this->adapter, 'foobar_');
		
		$this->assertEqual($db_with_prefix->Item, 'foobar_items');
		
		$db_without_prefix = new Omeka_Db($this->adapter);
		
		$this->assertEqual($db_without_prefix->Item, 'items');
	}
}
 
?>

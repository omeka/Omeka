<?php 
require_once 'Omeka/Db.php';
require_once 'Omeka/Context.php';
require_once 'Omeka/Db/Table.php';
require_once 'Inflector.php';
class Core_OmekaDbTest extends PHPUnit_Framework_TestCase
{
	protected $adapter;
		
	public function testDbCanRetrieveTableNameWithPrefix()
	{
	    // false as 5th argument makes the constructor not be called
	    $this->adapter = $this->getMock('Zend_Db_Adapter_Mysqli', array(), array(), '', false);

		$db_with_prefix = new Omeka_Db($this->adapter, 'foobar_');
		
		$this->assertEquals($db_with_prefix->Item, 'foobar_items');
		
		$db_without_prefix = new Omeka_Db($this->adapter);
		
		$this->assertEquals($db_without_prefix->Item, 'items');
	}
}
 
?>

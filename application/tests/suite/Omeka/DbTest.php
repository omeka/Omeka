<?php 
require_once 'Omeka/Db.php';
require_once 'Omeka/Db/Table.php';
require_once 'Inflector.php';
class Omeka_DbTest extends Omeka_Test_TestCase
{
    protected $adapter;

    public function testDbCanRetrieveTableNameWithPrefix()
    {
        // false as 5th argument makes the constructor not be called
        $this->adapter = $this->getMock('Zend_Db_Adapter_Mysqli', array(), array(), '', false);
        $this->adapter->method('getServerVersion')->will($this->returnValue('1.0.0'));

        $db_with_prefix = new Omeka_Db($this->adapter, 'foobar_');

        $this->assertEquals($db_with_prefix->Item, 'foobar_items');

        $db_without_prefix = new Omeka_Db($this->adapter);

        $this->assertEquals($db_without_prefix->Item, 'items');
    }
}

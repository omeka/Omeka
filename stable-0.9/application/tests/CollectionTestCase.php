<?php 
require_once 'Collection.php';
/**
* 
*/
class CollectionTestCase extends OmekaTestCase
{
	public function setUp()
	{
		return parent::setUp();
	}
	
	public function testCollectionRequiresName()
	{
		$c = new Collection;
		
		$this->assertFalse($c->isValid());
		
		//Make a string that is too long for a collection name
		$name = '';
		for ($i=0; $i < 257; $i++) { 
			$name .= 'F';
		}
		
		$c = new Collection;
		$c->name = $name;
		
		$this->assertFalse($c->isValid());
		
		$c = new Collection;
		$c->name = "Foobar";
		
		$this->assertTrue($c->isValid());
	}
}
 
?>

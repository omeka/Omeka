<?php 
	
class ItemTestCase extends OmekaTestCase
{
	
	public $i1;
	public $i2;
	public $u1;
	
    /**
	 * 2 Item, 2 User, id #1 has related data, #2 does not
	 *
	 * @return void
	 **/
	public function init()
	{
	}
	
	public function setUp()
	{
		parent::setUp();
		$this->i1 = $this->manager->getTable('Item')->find(1);
		$this->i1->refresh();
		$this->i2 = $this->manager->getTable('Item')->find(2);
		$this->i2->refresh();
		$this->u1 = $this->manager->getTable('User')->find(1);
		$this->u1->refresh();
	}
	
	public function testItemValidates()
	{

	}
	

	public function testAddTagString()
	{

	}
	
	public function testHasTag()
	{

	}
	
	public function testHasFavorite()
	{

	}
	
	public function testGetUserTags()
	{

	}
	
	public function testGetMetadata()
	{
		$i = $this->i1;
		$m = $i->metadata('Metafield1');
		$this->assertEqual($m, 'Metatext1');
		
		$m = $i->metadata('Metafield1', false);
		$this->assertTrue(($m instanceof Metatext));
		
		$i2 = $this->i2;
		$m = $i2->metadata('Metafield1');
		$this->assertNull($m);
		
		//Test for pulling inactive/active plugin metadata
	}
}
 
?>

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
		$sql = file_get_contents('users.sql');
		$this->manager->connection()->execute($sql);
		
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
		$i = $this->i1;
		$this->assertTrue($i->isValid());
		
		$i->title = null;
		$this->assertFalse($i->isValid());
		
		$i->title = '';
		$this->assertFalse($i->isValid());
	}
	

	public function testAddTagString()
	{
		$i = $this->i1;
		$u = $this->u1;
		$string = "foo, bar, far";
		$i->addTagString($string, $u);
		$this->assertEqual(count($i->ItemsTags), 4);
		$existingIt = $i->ItemsTags->getFirst();
		$invalidIt = $i->ItemsTags[1];
		$validNewIt = $i->ItemsTags[2];
		$this->assertFalse($invalidIt->exists());
		$this->assertFalse($invalidIt->isUnique(), 'Duplicate tag is flagged as unique (therefore valid)');
		$this->assertTrue($existingIt->isValid());
		$this->assertTrue($validNewIt->isUnique());
		$this->assertTrue($validNewIt->isValid());
	}
	
	public function testHasTag()
	{
		$i = $this->i1;
		$this->assertTrue($i->hasTag('foo'));
		
		$u = $this->u1;
		$this->assertTrue($i->hasTag('foo', $u));
		
		$newU = new User;
		$this->assertFalse($i->hasTag('foo', $newU), 'Item is tagged by a user that does not exist');
	}
	
	public function testHasFavorite()
	{
		$i = $this->i1;
		$u = $this->u1;
		$this->assertTrue($i->isFavoriteOf($u));
		
		$u2 = $this->manager->getTable('User')->find(2);
		$this->assertFalse($i->isFavoriteOf($u2));
	}
	
	public function testGetUserTags()
	{
		$i = $this->i1;
		$userWithTags = $this->u1;
		$userNoTags = $this->manager->getTable('User')->find(2);
		$tags = $i->userTags($userWithTags);
		$this->assertEqual(count($tags), 1);
		
		$tags = $i->userTags($userNoTags);
		$this->assertEqual(count($tags), 0);
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
	
	public function testSearchAggregation()
	{
		$i = $this->i1;
		$if = $i->ItemsFulltext;
		$t = new Tag;
		$t->name = 'newTagged';
		$i->Tags->Add($t);
		$if->aggregate();
		$agg = $if->text;
		$array = array_diff(explode(' ', $agg), array(""));
		sort($array);
		$this->assertEqual($array, array ( 0 => 'Item1', 1 => 'Metatext1', 2 => 'foo', 3 => 'newTagged'));
	}
}
 
?>

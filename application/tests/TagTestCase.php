<?php 
class TagTestCase extends OmekaTestCase
{	
	public function setUp()
	{
		parent::setUp('Tag');
		$table = Doctrine_Manager::getInstance()->getTable('Tag');
		$this->Tag1 = $table->find(1);
		$this->Tag2 = $table->find(2);
		$this->table = $table;
	}
	
	public function testFindAll()
	{
		//Assert that findAll returns a Doctrine_Collection for a record type
		$tags = $this->table->findAll('Item');
		$this->assertEqual(count($tags), 2);
		
		
		//Assert that only tags with taggings are returned
		$tags = $this->table->findAll();
		$this->assertEqual(count($tags), 3);
		
	}
	
	public function testFind()
	{
		$this->assertEqual("Tag1", $this->Tag1->name);
		
		//Assert that searching for one tag returns a Tag instance
		$foundTag = $this->table->find($this->Tag1->id);
		
		$this->assertEqual("Tag", get_class($foundTag));
		
		$foundTags = $this->table->find(array(1, 2));
		
		//Assert that searching for multiple tag ids returns a collection
		$this->assertEqual("Doctrine_Collection", get_class($foundTags));
		$this->assertEqual(2, count($foundTags));		
	}
	
	public function testFindBy()
	{
		//Assert retrieve by user
		$u = $this->fixtures['User2'];
		
		$tC = $this->table->findBy( array('user'=>$u), 'Item', true);
		
		$this->assertEqual($tC, 1);
		
		//Assert retrieve by record
		
		$i = $this->fixtures['Item'];
		
		$tC = $this->table->findBy( array('record'=>$i), 'Item', true);
		
		$this->assertEqual($tC, 2);
		
		//Assert retrieve by entity
		
		$e = $this->fixtures['Entity'];
		
		$tC = $this->table->findBy( array('entity'=>$e), 'Item', true);
		
		$this->assertEqual($tC, 2);
		
		//Assert retrieve public
		
		$tC = $this->table->findBy( array('public'=>true), 'Item', true);
		
		$this->assertEqual($tC, 0);
		
		//Assert limit retrieval
		
		$tags = $this->table->findBy( array('limit'=>1), null );
		
		$this->assertEqual(count($tags), 1);
		
		//Assert order by recent
		
		$tags = $this->table->findBy( array('recent'=>true), null);
		
		$mostRecent = array_pop($tags);
		
		$this->assertEqual($mostRecent['id'], 1);
		
		//Assert order from most to least
		
		$tags = $this->table->findBy( array('mostToLeast'=>true), null);
		
		$least = end($tags);
		
		$this->assertEqual($least['id'], 3);
		
	}
	
	public function testFindOrNew()
	{
		//Assert finds persistent tag		
		$tag = $this->table->findOrNew('Tag1');
		
		$this->assertTrue( $tag->exists() );
		$this->assertEqual($tag->id, $this->fixtures['Tag1']->id);
		
		//Assert makes new tag
		
		$tag = $this->table->findOrNew('Foobar');
		$this->assertFalse( $tag->exists() );
		
	}
	
	public function testDeleteByUser()
	{
		
	}
	
	public function testRename()
	{
		
	}
} 
?>

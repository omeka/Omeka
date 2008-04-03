<?php 
class TagTestCase extends OmekaTestCase
{	
	public function setUp()
	{
		parent::setUp();
		
		$this->db->setTable('Tag', false);
	}
	
	public function testFindOrNew()
	{
		$this->setUpLiveDb();
		
		$tag = new Tag;
		$tag->name = 'foo';
		
		$tag->save();
		
		$this->assertTrue((bool) $tag->id);
		
		$found_tag = $this->db->getTable('Tag')->findOrNew('foo');
		
		$this->assertEqual($found_tag->name, 'foo');
		
		//Assert does not find persistent tag, but instead returns a new one with that name		
		$new_tag = $this->db->getTable('Tag')->findOrNew('Tag1');
		
		$this->assertFalse( $new_tag->exists() );
		$this->assertEqual($new_tag->name, 'Tag1');	
	}

	public function testFind()
	{
		$db = $this->db;
	
		$table = $db->getTable('Tag');
		
		$db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount 
FROM tags t 
INNER JOIN taggings tg ON tg.tag_id = t.id 
WHERE t.id = '1' 
GROUP BY t.id 
LIMIT 1");
		
		//Assert that searching for one tag returns a Tag instance
		$foundTag = $table->find(1);
	}

	

	public function testFindByUser()
	{
		$db = $this->db;	
		
		$table = $this->db->getTable('Tag');
				
		//Test that findBy() creates the proper SQL statement when returning the count for a user

		$db->expectCountQuery("SELECT COUNT(DISTINCT(t.id)) 
FROM tags t INNER JOIN taggings tg ON tg.tag_id = t.id 
INNER JOIN entities e ON e.id = tg.entity_id 
INNER JOIN users u ON u.entity_id = e.id 
INNER JOIN items i ON i.id = tg.relation_id 
WHERE tg.type = 'Item' AND u.id = '1'");
		
		$tags = $table->findBy( array('user'=>1, 'return'=>'count'), 'Item');
	}

	
	public function testFindByItem()
	{
		$db = $this->db;
		
		$item = new Item;
		$item->id = 2;
		
		$table = $db->getTable('Tag');
		
		$db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount 
FROM tags t 
INNER JOIN taggings tg ON tg.tag_id = t.id 
WHERE tg.relation_id = '2' AND tg.type = 'Item' 
GROUP BY t.id");
		
		$table->findBy(array('record'=>$item));
	}	

		
	public function testFindByEntity()
	{
						
		$this->db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount 
FROM tags t 
INNER JOIN taggings tg ON tg.tag_id = t.id 
INNER JOIN entities e ON e.id = tg.entity_id 
INNER JOIN items i ON i.id = tg.relation_id 
WHERE tg.type = 'Item' AND e.id = '2' 
GROUP BY t.id");
		
		$this->db->getTable('Tag')->findBy( array('entity'=>2), 'Item');		
	}	


	public function testFindByPublic()
	{
		//Assert retrieve public
		
		$this->db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount 
FROM tags t 
INNER JOIN taggings tg ON tg.tag_id = t.id 
INNER JOIN items i ON i.id = tg.relation_id 
WHERE tg.type = 'Item' AND i.public = 1 
GROUP BY t.id");
		
		$this->db->getTable('Tag')->findBy( array('public'=>true), 'Item');		
	}	

		
	public function testLimitFindBy()
	{
		$this->db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount 
FROM tags t 
INNER JOIN taggings tg ON tg.tag_id = t.id 
GROUP BY t.id 
LIMIT 5");
		
		$this->db->getTable('Tag')->findBy( array('limit'=>5), null );
	}	

	
	public function testFindByRecent()
	{
		
		$this->db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount 
FROM tags t 
INNER JOIN taggings tg ON tg.tag_id = t.id 
INNER JOIN items i ON i.id = tg.relation_id 
WHERE tg.type = 'Item' 
GROUP BY t.id 
ORDER BY tg.time DESC");
		
		$this->db->getTable('Tag')->findBy( array('sort'=>'recent'), 'Item');		
	}	
			
	public function testOrderedMostToLeast()
	{		
	$this->db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount 
FROM tags t 
INNER JOIN taggings tg ON tg.tag_id = t.id 
GROUP BY t.id 
ORDER BY tagCount DESC");
		
		$this->db->getTable('Tag')->findBy( array('sort'=>'most'), null);		
	}	
/*
	public function testFindAll()
	{		
		//Assert that findAll returns a Doctrine_Collection for a record type
		$this->db->expectQuery(
"SELECT t.*, COUNT(t.id) as tagCount
FROM tags t
INNER JOIN taggings tg ON tg.tag_id = t.id
INNER JOIN items i ON i.id = tg.relation_id
WHERE tg.type = 'Item'
GROUP BY t.id
ORDER BY tagCount ASC", array());

		$this->db->getTable('Tag')->findAll('Item');		
	}	
	
	public function testRename()
	{
		
	}
*/	
} 
?>

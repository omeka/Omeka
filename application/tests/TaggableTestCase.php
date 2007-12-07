<?php 
/**
* TaggableTestCase
*/
class TaggableTestCase extends OmekaTestCase
{
	public function setUp()
	{
		include 'dependencies.php';

		//Let's use a real DB object for these tests
		$this->setUpLiveDb();
	}
	
	public function testDiffTagString()
	{
		$t1 = new Tag;
		$t1->name = "Tag1";
		
		$t2 = new Tag;
		$t2->name = "Tag2";
		
		$t3 = new Tag;
		$t3->name = "Tag3";
		
		$tags = array($t1, $t2, $t3);
		
		$item = new Item;
								
		$diff = $item->diffTagString('Tag1, Tag2, foo', $tags);
		
		$this->assertEqual($diff['removed'][0], 'Tag3');
		$this->assertEqual($diff['added'][0], 'foo');
	}
	

	public function testAddTags()
	{	
		$item = $this->getItem();
		
		$entity = $this->getEntity();
		
//		$before_tag_count = count($item->getTags());
		
		$item->addTags('foo,    bar', $entity);
		
//		$after_tag_count = count($item->getTags());
		
//		$this->assertEqual($before_tag_count + 2, $after_tag_count);

		$tags = $item->getTags();
		
		$this->assertEqual(count($tags), 2);
		
		$this->assertEqual($tags[1]->name, 'bar');
	}
	
	private function getItem()
	{
		$item = new Item;
		$item->title = "New Test Item";	
		$item->save();
		
		return $item;
	}
	
	private function getEntity()
	{
		$entity = new Entity;
		$entity->type = "Person";
		$entity->first_name = "Chuck";
		$entity->last_name = "Klosterman";
		$entity->save();
		
		return $entity;		
	}
	
	public function testDeleteTags()
	{		
		$item = $this->getItem();
		$entity = $this->getEntity();
		
		//There are no tags in the system, let's add some
		
		$item->addTags('foo, bar', $entity);
				
		$tags = array('foo');
	
		//Delete a single tag for this entity
		$item->deleteTags($tags, $entity);
	
		$remaining_tags = $item->entityTags($entity);
		
		$this->assertEqual($remaining_tags[0]->name, "bar");
	}
	
	public function testApplyTagString()
	{		
		$entity = $this->getEntity();
		$item = $this->getItem();		
	
		$item->applyTagString('foo,       bar,', $entity);

		$tag_count = count($item->getTags());
		
		$this->assertEqual($tag_count, 2);
		
		$item->applyTagString('bar, crap', $entity);
		
		$new_tags = $item->getTags();
		
		$tag_count = count($new_tags);
		
		$this->assertEqual($tag_count, 2);
		
		$this->assertEqual($new_tags[1]->name, "crap");

	}
	
}
 
?>

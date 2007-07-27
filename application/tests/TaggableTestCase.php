<?php 
/**
* TaggableTestCase
*/
class TaggableTestCase extends OmekaTestCase
{
	public function setUp()
	{
		parent::setUp('Tag');
	}
	
	public function testDiffTagString()
	{
		$tags = $this->fixtures['All Tags'];
		
		$item = $this->fixtures['Item'];
		
		$diff = $item->diffTagString('Tag1, Tag2, foo', $tags);
		
		$this->assertEqual($diff['removed'][0], 'Tag3');
		$this->assertEqual($diff['added'][0], 'foo');
	}
	
	public function testAddTags()
	{
		$item = $this->fixtures['Item'];
		
		$tagsCount = count($item->Tags);
		
		$entity = $this->fixtures['Entity'];
		
		$taggingsCount = count($this->fixtures['Entity Taggings']);
		
		//Add the tags
		$item->addTags('foo, bar', $entity);
		
		$tags = $item->Tags;
		
		//Assert 2 tags have been added
		$this->assertEqual(count($tags), $tagsCount + 2);
		
		//Assert 2 taggings have been added for this entity
		$taggings = Doctrine_Manager::getInstance()->getTable('Taggings')->findBy(array('entity'=>$entity));
		
		$this->assertEqual(count($taggings), $taggingsCount + 2);
	
		$this->wipeDb('Tag');
	}
	
	public function testDeleteTags()
	{
		$item = $this->fixtures['Item'];
		$entity = $this->fixtures['Entity'];
		
		$taggingsCount = count($item->entityTags($entity));
				
		$tags = array('Tag1', 'foo');
		
		//Delete only taggings for this entity
		$item->deleteTags($tags, $entity);
		
		$newTaggingsCount = count($item->entityTags($entity));
		
		$this->assertEqual($taggingsCount - 1, $newTaggingsCount);
	}
	
	public function testApplyTagString()
	{
		$tag = $this->fixtures['Tag1'];
		
		$entity = $this->fixtures['Entity'];
		
		$item = $this->fixtures['Item'];
		
	}
}
 
?>

<?php 
class TaggingsTestCase extends OmekaTestCase
{	
	public function setUp()
	{
		parent::setUp('Tagging');
		$this->table = Doctrine_Manager::getInstance()->getTable('Taggings');
	}
	
	public function testFindBy()
	{
		//Assert that searching by Tag instance, tag name, Doctrine_Collection, array works
		
		//Search by Tag instance
		
		$tag1 = $this->fixtures['Tag1'];
		
		$taggings = $this->table->findBy(array('tag'=>$tag1));
		
		//Assert that Tag instance pulls correctly
		$this->assertEqual(count($taggings), 4);
		
		$taggings = $this->table->findBy(array('tag'=>$tag1), 'Item');
		
		
		//Assert that narrowing by a specific record type works
		$this->assertEqual(count($taggings), 2);
		
		
		//Assert that pulling by tag name works
		$taggings = $this->table->findBy(array('tag'=>'Tag1'));
		
		$this->assertEqual(count($taggings), 4);
		
		//Assert that pulling by Doctrine_Collection of Tags works
		$taggingsCount = $this->table->findBy( array('tag'=>$this->fixtures['All Tags']), 'Item', true);
		
		$itemTagCount = $this->fixtures['taggingCount']['Items'];
		
		$this->assertEqual($taggingsCount, $itemTagCount);	
		
		//Assert that pulling by array of tag names works
		$taggingsCount = $this->table->findBy( array('tag'=>array('Tag1', 'Tag2')), 'Item', true);
		
		$this->assertEqual($taggingsCount, $itemTagCount);
		
		
		//Assert that pulling by passing a record works
		$i = $this->fixtures['Item'];
		$tC = $this->table->findBy( array('record'=>$i), null, true);
		
		$this->assertEqual($tC, 2);
		
		//Assert that pulling by record and tag works
		$tC = $this->table->findBy( array('record'=>$i, 'tag'=>'Tag1'), null, true);
		
		$this->assertEqual($tC, 1);
		
		//Assert pulling by entity
		$e = $this->fixtures['Entity'];
		$tC = $this->table->findBy( array('entity'=>$e), null, true);
		
		$this->assertEqual($tC, 5);
		
		//Assert pulling by user
		$u = $this->fixtures['User'];
		$tC = $this->table->findBy( array('user'=>$u), null, true);
		
		$this->assertEqual($tC, 5);
	}
}
?>
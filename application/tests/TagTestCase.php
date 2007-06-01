<?php 
require_once 'Exhibit.php';
require_once 'Tag.php';
Mock::generate('Exhibit');	
Mock::generate('Tag');
class TaggableTestCase extends OmekaTestCase
{	
	public function setUp()
	{
		parent::setUp();
		$table = $this->manager->getTable('Tag');
		$this->t1 = $table->find(1)->refresh();
		$this->t2 = $table->find(2)->refresh();
		$this->e1 = $this->manager->getTable('Exhibit')->find(1);
		$this->e1->refresh();
//		$this->table = $table;
	}
	
	public function testDiffTagString()
	{
		$table = $this->manager->getTable('Tag');
		$e = $this->e1;
		$string = "foo,bar,Tag1";
		$ret = $e->diffTagString($string);
		$this->assertEqual(
			array('added'=>array('foo','bar'),'removed'=>array('Tag2')),
			$ret);
		
		//String with spaces	
		$string	= "foo, bar, Tag1";
		$ret = $e->diffTagString($string);
		$this->assertEqual(
			array('added'=>array('foo','bar'), 'removed'=>array('Tag2')),
			$ret);
			
		//array of tags
		$tags = array('Tag1','Tag2');
		$ret = $e->diffTagString($string,$tags);
		$this->assertEqual(
			array('added'=>array('foo','bar'),'removed'=>array('Tag2')),
			$ret);
	}
	
	public function testTagString()
	{
		$e = $this->manager->getTable('Exhibit')->find(1);
		$this->assertEqual("Tag1, Tag2", $e->tagString());
	}
	
	public function testGetTagCount()
	{
		$e = $this->manager->getTable('Exhibit')->find(1);
		
		$this->assertEqual( $e->getTagCount('Tag1'), 2);
	}
	
	public function testDeleteTag()
	{
		$e = $this->manager->getTable('Exhibit')->find(1);
/*		
		$e = new MockExhibit();
		
		$taggable = new Taggable($e);
		
		$t1 = new MockTag;
		$t2 = new MockTag;
		$t3 = new MockTag;
		
		$t1->name = 'foo';
		$t1->setReturnValue('exists', true);
		
		$t2->name = 'bar';
		$t2->setReturnValue('exists', true);
		
		$t3->name = 'baz';
		$t3->setReturnValue('exists', false);
		
		$e->Tags = array($t1,$t2,$t3);
		
		Zend::dump( $taggable->hasTag('foo') );
*/	
/*		
		//Delete one of the two instances of Tag1
		$user_id = 1;
		$returnVal = $e->deleteTag('Tag1', $user_id);
		
		$this->assertTrue($returnVal);
	
		//Cannot delete again for this user
		$returnVal = $e->deleteTag('Tag1', $user_id);
		
		$this->assertFalse($returnVal);
				 	
		//Cannot delete from invalid user
		$returnVal = $e->deleteTag('Tag1', 1000);
		
		$this->assertFalse($returnVal);
		
		//Delete all instances of tag regardless of user
		//This should delete the other instance of Tag1
		$returnVal = $e->deleteTag('Tag1', null, true);
		
		$this->assertTrue($returnVal);
		
		//There should be no instances of Tag1
		$sql = "SELECT COUNT(*) as count FROM exhibits_tags j INNER JOIN tags t WHERE j.exhibit_id = 1 AND t.name = 'Tag1'";
		$count = $this->manager->connection()->fetchOne($sql);
		$this->assertEqual($count, 0);
*/	}

	public function testRemoveTagsByArray()
	{	
/*		$e = $this->manager->getTable('Exhibit')->find(1);
		
		$this->assertEqual(2, $e->Tags->count());
		
		foreach ($e->Tags as $tag) {
			echo Doctrine_Lib::getRecordAsString($tag);
			Zend::dump( $tag->id );
		}
		
		//Delete an array of tags
		$user_id = 1;
		$e->removeTagsByArray(array('Tag1','Tag2','foo','bar'), 1, false);
		
		$this->assertEqual(1, $e->Tags->count());
*/	}

	public function testApplyTagString()
	{
		
	}
	
	
	
	public function testFindSome()
	{
		$table = $this->manager->getTable('Tag');
		$e = $this->e1;
		
		//Tags for Items that are also tagged for that exhibit
		$tags = $table->findSome(array('exhibit_id'=>1),$for="Item");
		$this->assertEqual('Tag1',$tags->getFirst()->name);
		
		//Tags for Items for a single Item
		$tags = $table->findSome(array('item_id'=>1));
		$this->assertEqual('Tag1',$tags->getFirst()->name);
		
		//Tags for Exhibits
		$tags = $table->findSome(array(),"Exhibit");
		$this->assertEqual(2,$tags->count());
		
		//Tags for Items
		$tags = $table->findSome();
		$this->assertEqual(1,$tags->count());
		
		//Recent tags for Exhibits
		$tags = $table->findSome(array('recent'=>true),"Exhibit");
		$this->assertEqual('Tag2',$tags->getFirst()->name);
		
		 //Alphabetical for Exhibits
		$tags = $table->findSome(array('alpha'=>true),"Exhibit");
		$this->assertEqual('Tag1',$tags->getFirst()->name);
	}

	
	
	
	
	
	
	
	
	
}
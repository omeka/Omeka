<?php 
class ExhibitTestCase extends OmekaTestCase
{ 
	function testExhibitConstraints()
	{
		$table = $this->db->getTable('Exhibit');
		
		//Test the validity of the exhibit itself
		$e = new Exhibit;
		
		$this->assertFalse($e->isValid());
		
		$e->slug = 'foobar-exhibit';
		
		$this->assertFalse($e->isValid());

		$e->title = 'Foobar Exhibit';
		
		$this->assertTrue($e->isValid());
				

		//Test the validity of an exhibit's section
		$s = $e->Sections[0];
		
		$this->assertFalse($s->isValid());
		
		$s->title = "Foobar";
		
		$this->assertFalse($s->isValid());
		
		$s->order = 1;
		
		$this->assertFalse($s->isValid());
		
		$s->exhibit_id = 1;
		
		$this->assertTrue($s->isValid());
		
		
		//Test the validity of a single page for a section of an exhibit
		$p = $s->Pages[0];
		
		$this->assertFalse($p->isValid());
		
//		$stack = $p->getErrorStack();
		
//		Zend_Debug::dump( $stack );
		
		$p->layout = 'foobar';
		
		$p->section_id = 1;
		
		$p->order = 1;
		
		$this->assertTrue($p->isValid());
		
		
		//Test the validity of an entry for a page of a section of an exhibit
		$ip = $p->ExhibitPageEntry[0];
		
		$this->assertFalse($ip->isValid());
		
		$ip->page_id = 1;
		
		$ip->order = 1;
		
		$this->assertTrue($ip->isValid());
	}
	
	public function testExhibitDeleteTag()
	{
/*		$e = $this->db->getTable('Exhibit')->find(1);
		$this->assertEqual($e->Tags->count(), 2);
		
		$e->deleteTags('Tag1');
		
		$this->assertEqual($e->Tags->count(), 1);
*/	}
	
	public function testIsTaggable()
	{
		$e = $this->db->getTable('Exhibit')->find(1);
		$this->assertTrue($e->hasTag('Tag1'));
	}
}
?>

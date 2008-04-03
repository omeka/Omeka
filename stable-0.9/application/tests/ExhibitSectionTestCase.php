<?php 
/**
* 
*/
class ExhibitSectionTestCase extends OmekaTestCase
{
	public function setUp()
	{
		include 'dependencies.php';
		$this->setUpLiveDb();
	}
	
	private function getExhibit()
	{
		$exhibit = new Exhibit;
		$exhibit->title = "Foobar";
		$exhibit->save();
		
		return $exhibit;
	}
	
	public function testSavingMiniFormAutogeneratesSlug()
	{
		$post = array('title'=>'A New Section');
		
		$exhibit = $this->getExhibit();
	
		$section = new ExhibitSection;
		
		$exhibit->addChild($section);
								
		$section->saveForm($post);

		$this->assertTrue($section->exists());
		
		$this->assertEqual($section->slug, "a-new-section");
	}
	
	public function testSavingFullFormTransformsSlug()
	{
		$post = array('title'=>'A New Section', 'slug'=>' foo bar ');
		
		$exhibit = $this->getExhibit();
		
		$section = new ExhibitSection;
		
		$exhibit->addChild($section);
		
		$section->saveForm($post);
		
		$this->assertTrue($section->exists());
		
		$this->assertEqual($section->slug, 'foo-bar');
	}
	
	public function testDeletingAPageReordersPages()
	{
		$e = $this->getExhibit();
		
		$s = new ExhibitSection;
		$s->title = "Whatever";
		$s->slug = "whatever";
		$e->addChild($s);
		$s->save();
		
		for($i=1; $i<=5; $i++) {
			$p = new ExhibitPage;
			$p->layout = $i . '-page-layout';
			$s->addChild($p);
			$p->save();
		}
		
		$pages = $s->Pages;
		
		$lastPage = end($pages);
		
		$this->assertEqual($lastPage->order, 5);
		
		//Delete the 3rd page
		$pages[2]->delete();	
		
		$pages = $s->loadOrderedChildren();
		
		$this->assertEqual(count($pages), 4);
		
		$this->assertEqual(end($pages)->order, 4);	
	}
	
	public function testSectionSlugIsUnique()
	{
		//Test to see if a lone slug is unique
		$e = $this->getExhibit();
		$s1 = new ExhibitSection;
		$s1->title = "whatever";
		$s1->slug = "whatever";
		$e->addChild($s1);
		$s1->save();
		
		$this->assertTrue($s1->isValid());
		
		//Make another exhibit, with a section with the same slug as the previous
		//Test to see if the new section validates as unique
		
		$e = new Exhibit;
		$e->title = "Whatever";
		$e->forceSave();
		
		$s = new ExhibitSection;
		$s->title = "whatever";
		$s->slug = "whatever";
		$e->addChild($s);
		$s->forceSave();
		
		$this->assertTrue($s->isValid());
		
		//Make a section that is:
		// 1) Not persistent yet
		// 2) Has same slug as a persistent section
		// 3) is invalid
		
		$s = new ExhibitSection;
		$s->title = "whatever";
		$s->slug = "whatever";
		$e->addChild($s);
		
		$this->assertFalse($s->isValid());
	}
	
	
	public function testCanSaveTwoSectionsWithSameTitle()
	{
		$e = $this->getExhibit();
		
		$s1 = new ExhibitSection;
		$s1->title = "whatever";
		$s1->slug = "whatever";
		$e->addChild($s1);
		
		$s1->forceSave();
		
		$s2 = new ExhibitSection;
		$s2->title = "whatever";
		$s2->slug = "whatever2";
		
		$e->addChild($s2);
		
		$s2->forceSave();		

	}

}
 
?>

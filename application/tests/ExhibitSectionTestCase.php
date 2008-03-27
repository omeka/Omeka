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

}
 
?>

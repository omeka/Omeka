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

}
 
?>

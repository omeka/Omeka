<?php 
	
class ItemTestCase extends OmekaTestCase
{
	public function setUp()
	{
		parent::setUp('Item');
	}
	
	public function testSetMetatext() {
		$i = $this->fixtures['Valid Item'];
		
		$mt = $i->getMetatext("Bazfoo's Metafield");
		
		$this->assertFalse($mt);
		
		$i->setMetatext("Bazfoo's Metafield", "This is the metatext for Bazfoo's Metafield");
		
		$mt = $i->getMetatext("Bazfoo's Metafield");
		
		$this->assertNotNull($mt);
	}
}
 
?>

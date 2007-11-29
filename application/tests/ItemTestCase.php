<?php 
Mock::generate('MetatextTable');

class ItemTestCase extends OmekaTestCase
{
	public function setUp()
	{
		include 'dependencies.php';
		
		$mtTable = new MockMetatextTable;
		
		$mt = new Metatext;
		$mt->text = "This is text";
		
		$otherMt = new Metatext;
		$otherMt->text = "Whatever";
		
		//Assume that MetatextTable::findByItem() returns this arbitrary data for 2 metafields
		$mtTable->setReturnValue('findByItem', array("Bazfoo's Metafield"=>$mt, "Text"=>$otherMt));
		
		$this->db->setTable('Metatext', $mtTable);		
	}
	
	public function testGetMetatext()
	{
		$item = new Item;
		
		$mt = $item->getMetatext("Bazfoo's Metafield");
		
		$this->assertEqual($mt, "This is text");
	}
	

	public function testSetMetatext()
	{	
		$item = new Item;
		
		$item->setMetatext("Bazfoo's Metafield", "Modified text");
		
		$mt = $item->getMetatext("Bazfoo's Metafield");
		
		$this->assertEqual($mt, "Modified text");
	}
}
 
?>

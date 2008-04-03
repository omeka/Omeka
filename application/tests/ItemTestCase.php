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
		
	//This one involves actual data retrieval so we have to set up a live DB connection
	public function testCanRetrieveTypeMetadataForItems()
	{
		$this->setUpLiveDb();
		
		//Create a valid type
		$type = new Type;
		$type->name = "Valid Type";
		$type->forceSave();
		
		//Associate a couple of metafields with it
		$mf1 = new Metafield;
		$mf1->setArray(array('name'=>'Metafield with text'));
		$mf1->forceSave();
		
		$mf2 = new Metafield;
		$mf2->setArray(array('name'=>'Metafield without text'));
		$mf2->forceSave();
		
		//This metafield has no type associated with it
		$mf3 = new Metafield;
		$mf3->name = "Metafield without a type";
		$mf3->save();
		
		$tm1 = new TypesMetafields;
		$tm1->setArray(array('type_id'=>$type->id, 'metafield_id'=>$mf1->id));
		$tm1->forceSave();
		
		$tm2 = new TypesMetafields;
		$tm2->setArray(array('type_id'=>$type->id, 'metafield_id'=>$mf2->id));
		$tm2->forceSave();
		
		$item = new Item;
		$item->setArray(array('title'=>'Valid Item', 'type_id'=>$type->id));
		$item->forceSave();
		
		$mt1 = new Metatext;
		$mt1->setArray(array('metafield_id'=>$mf1->id, 'text'=>'Foobar!', 'item_id'=>$item->id));
		$mt1->forceSave();
		
		//This piece of metatext is for a metafield that has no corresponding type, so it shouldn't be returned
		$mt2 = new Metatext;
		$mt2->setArray(array('metafield_id'=>$mf3->id, 'text'=>'You will never see this text!', 'item_id'=>$item->id));
		$mt2->forceSave();
		
		//Assert that we get a nice big array of stuff
		$metadata = $item->FormTypeMetadata;
		
		$this->assertTrue(is_array($metadata));
		
		//Assert that the keys of the array are the names of the metafields
		$this->assertTrue(array_key_exists('Metafield with text', $metadata));
		$this->assertTrue(array_key_exists('text', $metadata['Metafield with text']));
		
		//Assert that the array contains all the metafields, but contains text=null where there is no saved metatext yet
		$this->assertEqual($metadata['Metafield with text']['text'], 'Foobar!');
		$this->assertEqual($metadata['Metafield without text']['text'], null);
		
		//Assert that it did not retrieve the metatext that was not associated with the item type
		$this->assertFalse(array_key_exists('Metafield without a type', $metadata));
	}
}
 
?>

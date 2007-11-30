<?php 
/**
* 
*/
class OmekaRecordTestCase extends OmekaTestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->db->setTable('Item', false);
	}
	
	public function testSaveFormFiresAllCallbacks()
	{
		$broker = get_plugin_broker();
		
		$record = new Item;
		
		$post = array('title'=>'Here is a title');
		
		//The object that gets passed around is an ArrayObject so as to make it pass-by-reference
		$passed_post = new ArrayObject($post);
		
		$broker->expectHooks(array('before_save_form_item'), array($record, $passed_post));
		
		$broker->expectHooks(array(
				'before_validate_item',
				'after_validate_item',
				'before_insert_item',
				'before_save_item',
				'after_insert_item',
				'after_save_item'), array($record));
		
		$broker->expectHooks(
			array('after_save_form_item'), array($record, $passed_post));
		
		$record->saveForm($post);
	}
}
 
?>

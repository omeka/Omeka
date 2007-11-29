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
		
		$broker->expectHooks(array(
				'pre_save_form_item',
				'pre_validate_item',
				'post_validate_item',
				'pre_insert_item',
				'pre_save_item',
				'post_insert_item',
				'post_save_item'), array($record));
		
		$broker->expectHooks(
			array('post_save_form_item'), array($record, $post));
		
		$record->saveForm($post);
	}
}
 
?>

<?php 
/**
* 
*/
class TypeTestCase extends OmekaTestCase
{
	public function testSavingTypesMetafieldsJoins()
	{
		$tm = new TypesMetafields;
		$tm->type_id = 1;
		$tm->metafield_id = 2;
		
		$this->db->setTable('TypesMetafields', false);
		
		$this->db->expect('insert', array('TypesMetafields', array('type_id'=>1, 'metafield_id'=>2, 'plugin_id'=>null, 'id'=>null)));
		
		$tm->save();
	}
}
 
?>

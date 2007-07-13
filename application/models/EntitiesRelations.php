<?php
require_once 'EntityRelationships.php';
define('ITEM_RELATION_INHERITANCE_ID', 1);
define('COLLECTION_RELATION_INHERITANCE_ID', 2);
/**
 * ItemsPeople
 * @package: Omeka
 */
class EntitiesRelations extends Kea_Record
{
    public function setTableDefinition()
    {
		$this->hasColumn('entity_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('relation_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('relationship_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('inheritance_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('time', 'timestamp');
		
//		$this->index('unique', array('fields'=>array('entity_id', 'relation_id', 'inheritance_id'), 'type'=>'unique'))
    }
    public function setUp()
    {
		$this->hasOne('Entity', 'EntitiesRelations.entity_id');
		$this->hasOne('EntityRelationships', 'EntitiesRelations.relationship_id');
    }

	public function preInsert()
	{
		$this->time = date('YmdHis');
	}
}

?>
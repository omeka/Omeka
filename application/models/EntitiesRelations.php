<?php
require_once 'EntityRelationships.php';
/**
 * ItemsPeople
 * @package: Omeka
 */
class EntitiesRelations extends Omeka_Record
{
    public function setTableDefinition()
    {
		$this->hasColumn('entity_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('relation_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('relationship_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('type', 'string', 50, array('notblank'=>true));
		$this->hasColumn('time', 'timestamp');
		
    }
    public function setUp()
    {
		$this->hasOne('Entity', 'EntitiesRelations.entity_id');
		$this->hasOne('EntityRelationships', 'EntitiesRelations.relationship_id');
    }

	//@todo Move this to CURRENT_TIMESTAMP() SQL
	public function preInsert()
	{
		$this->time = date('YmdHis');
	}
}

?>

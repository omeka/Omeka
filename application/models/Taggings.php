<?php
require_once 'TaggingsTable.php';
/**
 * Taggings
 * @package: Omeka
 */
class Taggings extends Kea_Record
{
	protected static $_inheritance_id;
	
    public function setTableDefinition()
    {
		$this->hasColumn('relation_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('tag_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('entity_id', 'integer', null, array('range'=>array('1')));
		$this->hasColumn('type', 'string', null, array('notnull'=>true, 'notblank'=>true));
		$this->hasColumn('time', 'timestamp');
    }

	public function setUp()
	{
		$this->hasOne('Entity', 'Taggings.entity_id');
		$this->hasOne('Tag', 'Taggings.tag_id');
	}
}

?>
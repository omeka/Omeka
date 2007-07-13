<?php
/**
 * EntityRelationships
 * @package: Omeka
 */
class EntityRelationships extends Kea_Record
{
    public function setTableDefinition()
    {
		$this->setTableName('entity_relationships');
		$this->hasColumn('name', 'string');
		$this->hasColumn('description', 'string');
    }
    public function setUp()
    {
		$this->hasMany('EntitiesRelations', 'EntitiesRelations.relationship_id');
    }
}

?>
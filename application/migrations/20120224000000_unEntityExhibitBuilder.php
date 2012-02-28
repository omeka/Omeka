<?php
/**
 * Remove Entity dependence from Exhibit Builder, if the tables exist.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class unEntityExhibitBuilder extends Omeka_Db_Migration
{
    public function up()
    {
        if (!$this->_isEbInstalled()) {
            return;
        }
        $this->_updateSchema();
        $this->_setExhibitData();
    }

    private function _isEbInstalled()
    {
        $table = $this->db->describeTable($this->db->Exhibit);
        return !empty($table);
    }
    
    private function _updateSchema()
    {
        $this->db->queryBlock(<<<SQL
ALTER TABLE `{$this->db->Exhibit}`
ADD `added` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
ADD `modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
ADD `owner_id` INT UNSIGNED DEFAULT NULL,
ADD INDEX `owner_id` (`owner_id`);
SQL
);
    }

    private function _setExhibitData()
    {
        $this->db->query(<<<SQL
UPDATE `{$this->db->Exhibit}` e
INNER JOIN `{$this->db->EntitiesRelations}` er ON er.relation_id = e.id
INNER JOIN `{$this->db->EntityRelationships}` ers ON ers.id = er.relationship_id
INNER JOIN `{$this->db->User}` u ON u.entity_id = er.entity_id
SET e.owner_id = u.id, e.added = er.time
WHERE ers.name = 'added' AND er.type = 'Exhibit'
SQL
);

        $this->db->query("UPDATE `{$this->db->Exhibit}` SET modified = NOW()");
    }
}

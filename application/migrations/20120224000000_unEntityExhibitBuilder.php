<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Remove Entity dependence from Exhibit Builder, if the tables exist.
 * 
 * @package Omeka\Db\Migration
 */
class unEntityExhibitBuilder extends Omeka_Db_Migration_AbstractMigration
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
        try {
            $table = $this->db->describeTable($this->db->Exhibit);
            return !empty($table);
        } catch (Zend_Db_Exception $e) {
            // Zend's documentation says describeTable should return
            // an empty array if the table doesn't exist, but at least
            // for mysqli, it instead throws an exception, so we need
            // to handle that.
            return false;
        }
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

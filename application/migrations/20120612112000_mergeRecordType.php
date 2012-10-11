<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Merge the RecordType and ElementSet tables.
 * 
 * @package Omeka\Db\Migration
 */
class mergeRecordType extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->_updateElementSetsTable();
        $this->_updateElementTextsTable();
        $this->_removeObsoleteData();
    }

    private function _updateElementSetsTable()
    {
        $this->db->query(<<<SQL
ALTER TABLE `{$this->db->ElementSet}`
ADD `record_type` VARCHAR(50) DEFAULT NULL AFTER `record_type_id`
SQL
);

        $this->db->query(<<<SQL
UPDATE `{$this->db->ElementSet}` e
INNER JOIN `{$this->db->RecordType}` r ON r.id = e.record_type_id
SET e.record_type = NULLIF(r.name, 'All')
SQL
);
        $this->db->query("ALTER TABLE `{$this->db->ElementSet}` ADD INDEX `record_type` (`record_type`)");
    }

    private function _updateElementTextsTable()
    {
        $this->db->query(<<<SQL
ALTER TABLE `{$this->db->ElementText}`
ADD `record_type` VARCHAR(50) DEFAULT NULL AFTER `record_type_id`
SQL
);

        $this->db->query(<<<SQL
UPDATE `{$this->db->ElementText}` e
INNER JOIN `{$this->db->RecordType}` r ON r.id = e.record_type_id
SET e.record_type = r.name
SQL
);

        $this->db->query(<<<SQL
ALTER TABLE `{$this->db->ElementText}`
DROP INDEX `record_id`,
ADD INDEX `record_type_record_id` (`record_type`, `record_id`)
SQL
);
    }
    
    private function _removeObsoleteData()
    {
        $this->db->query("ALTER TABLE `{$this->db->ElementSet}` DROP `record_type_id`");
        $this->db->query("ALTER TABLE `{$this->db->Element}` DROP `record_type_id`");
        $this->db->query("ALTER TABLE `{$this->db->ElementText}` DROP `record_type_id`");
        $this->db->query("DROP TABLE `{$this->db->RecordType}`");
    }
}

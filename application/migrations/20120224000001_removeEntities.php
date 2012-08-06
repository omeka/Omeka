<?php
/**
 * Remove Entities and related tables from Omeka.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class removeEntities extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query(<<<SQL
DROP TABLE
`{$this->db->Entity}`, `{$this->db->EntitiesRelations}`,
`{$this->db->EntityRelationships}`
SQL
);
        $this->db->query("ALTER TABLE `{$this->db->User}` DROP `entity_id`");
    }
}

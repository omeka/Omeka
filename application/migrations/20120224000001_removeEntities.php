<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Remove Entities and related tables from Omeka.
 * 
 * @package Omeka\Db\Migration
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

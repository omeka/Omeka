<?php
/**
 * Remove entities from Tag system.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class unEntityTags extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->_updateSchema();
    }

    private function _updateSchema()
    {
        $this->db->query(<<<SQL
ALTER IGNORE TABLE `{$this->db->Taggings}`
DROP INDEX `tag`,
DROP `entity_id`,
ADD UNIQUE INDEX `tag` (`type`, `relation_id`, `tag_id`)
SQL
);
    }
}

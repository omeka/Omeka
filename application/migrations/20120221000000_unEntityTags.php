<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Remove entities from Tag system.
 * 
 * @package Omeka\Db\Migration
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

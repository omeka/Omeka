<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Migration
 */
class renameTaggings extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = "
        ALTER TABLE `{$this->db->prefix}taggings` 
        CHANGE `relation_id` `record_id` INT(10) UNSIGNED NOT NULL, 
        CHANGE `type` `record_type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''";
        $this->db->query($sql);
        
        $sql = "RENAME TABLE  `{$this->db->prefix}taggings` TO  `{$this->db->prefix}records_tags`";
        $this->db->query($sql);
    }
}

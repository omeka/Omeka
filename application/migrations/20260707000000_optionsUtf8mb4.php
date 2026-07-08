<?php
/**
 * Convert options value column to utf8mb4
 *
 * @package Omeka\Db\Migration
 */
class optionsUtf8mb4 extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->Option} MODIFY COLUMN `value` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
}

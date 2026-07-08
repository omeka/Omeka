<?php
/**
 * Convert element_texts text column to utf8mb4
 *
 * @package Omeka\Db\Migration
 */
class elementTextsUtf8mb4 extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->ElementText} MODIFY COLUMN `text` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }
}

<?php
/**
 * Convert search_texts title and text columns to utf8mb4
 *
 * @package Omeka\Db\Migration
 */
class searchTextsUtf8mb4 extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->SearchText}
            MODIFY COLUMN `title` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            MODIFY COLUMN `text` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL");
    }
}

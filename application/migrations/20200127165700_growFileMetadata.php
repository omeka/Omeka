<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2020 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Increase maximum size of stored embedded metadata for files
 *
 * @package Omeka\Db\Migration
 */
class growFileMetadata extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->File} MODIFY `metadata` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL");
    }
}

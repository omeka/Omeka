<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2022 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add alt text column to file table
 *
 * @package Omeka\Db\Migration
 */
class addFileAltText extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->File} ADD `alt_text` mediumtext collate utf8_unicode_ci");
    }
}

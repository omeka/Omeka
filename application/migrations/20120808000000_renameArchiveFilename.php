<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Rename archive _filename to filename.
 * 
 * @package Omeka\Db\Migration
 */
class renameArchiveFilename extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE  `{$this->db->File}` 
CHANGE `archive_filename` `filename` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
SQL;
        $this->db->query($sql);
    }
}

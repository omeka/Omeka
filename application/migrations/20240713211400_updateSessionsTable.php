<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2020 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Change sessions table to case-sensitive ascii IDs and add index on modified column
 *
 * @package Omeka\Db\Migration
 */
class updateSessionsTable extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->Session} MODIFY `id` varchar(128) collate ascii_bin, ADD INDEX `modified` (`modified`)");
    }
}

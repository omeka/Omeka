<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2022 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Increase length of salt and hash for user passwords
 *
 * @package Omeka\Db\Migration
 */
class growUserPassword extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE {$this->db->User} MODIFY `password` VARCHAR(255) COLLATE ascii_bin DEFAULT NULL");
    }
}

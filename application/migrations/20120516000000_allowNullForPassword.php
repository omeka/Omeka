<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Remove the NOT NULL restrictions on the "password" and column for users.
 * 
 * @package Omeka\Db\Migration
 */
class allowNullForPassword extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `{$this->db->User}`
MODIFY `password` VARCHAR(40) COLLATE utf8_unicode_ci DEFAULT NULL
SQL;

        $this->db->query($sql);
    }
}

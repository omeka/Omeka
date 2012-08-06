<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Remove the NOT NULL restrictions on the "password" and
 * column for users.
 *
 * @package Omeka
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

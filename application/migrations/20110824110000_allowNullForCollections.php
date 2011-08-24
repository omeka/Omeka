<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Remove the NOT NULL restrictions on the "description" and
 * "collectors" columns for collections.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class allowNullForCollections extends Omeka_Db_Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `{$this->db->Collection}`
MODIFY `description` TEXT COLLATE utf8_unicode_ci DEFAULT NULL,
MODIFY `collectors` TEXT COLLATE utf8_unicode_ci DEFAULT NULL;
SQL;

        $this->db->query($sql);
    }
}

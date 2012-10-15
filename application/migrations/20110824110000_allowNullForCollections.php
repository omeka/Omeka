<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Remove the NOT NULL restrictions on the "description" and "collectors" 
 * columns for collections.
 * 
 * @package Omeka\Db\Migration
 */
class allowNullForCollections extends Omeka_Db_Migration_AbstractMigration
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

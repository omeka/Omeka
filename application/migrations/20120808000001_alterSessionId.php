<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Alter the session ID.
 * 
 * @package Omeka\Db\Migration
 */
class alterSessionId extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE  `{$this->db->Session}`
CHANGE `id` `id` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT  ''
SQL;
        $this->db->query($sql);
    }
}

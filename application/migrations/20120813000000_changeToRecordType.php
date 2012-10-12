<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Change record_name to record_type.
 * 
 * @package Omeka\Db\Migration
 */
class changeToRecordType extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE  `{$this->db->SearchText}` 
CHANGE  `record_name`  `record_type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
SQL;
        $this->db->query($sql);
    }
}

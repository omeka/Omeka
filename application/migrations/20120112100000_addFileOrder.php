<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Add order field to file table.
 * 
 * @package Omeka\Db\Migration
 */
class addFileOrder extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE  `{$this->db->File}` ADD  `order` INT UNSIGNED NULL DEFAULT NULL AFTER  `item_id`;
SQL;
        $this->db->query($sql);
    }
}

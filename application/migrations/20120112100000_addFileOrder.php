<?php
/**
 * Add order field to file table.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class addFileOrder extends Omeka_Db_Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE  `{$this->db->File}` ADD  `order` INT UNSIGNED NULL DEFAULT NULL AFTER  `item_id`;
SQL;
        $this->db->query($sql);
    }
}

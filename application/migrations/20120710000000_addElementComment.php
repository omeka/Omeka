<?php
/**
 * Add element comment to schema.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2012
 */
class addElementComment extends Omeka_Db_Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE {$this->db->Element} 
ADD comment TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL
SQL;
        $this->db->query($sql);
    }
}

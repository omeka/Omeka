<?php
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

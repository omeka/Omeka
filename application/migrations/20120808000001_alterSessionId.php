<?php
class alterSessionId extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE  `omeka_sessions` 
CHANGE `id` `id` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT  ''
SQL;
        $this->db->query($sql);
    }
}

<?php
class renameArchiveFilename extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE  `{$this->db->File}` 
CHANGE `archive_filename` `filename` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
SQL;
        $this->db->query($sql);
    }
}

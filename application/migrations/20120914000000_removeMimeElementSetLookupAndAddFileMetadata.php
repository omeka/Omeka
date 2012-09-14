<?php
/**
 * Remove MimeElementSetLookup table and adds a metadata column to the File table. 
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class removeMimeElementSetLookupAndAddFileMetadata extends Omeka_Db_Migration_AbstractMigration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `{$this->db->File}` ADD `metadata` text collate utf8_unicode_ci NOT NULL");     
        $this->db->query(<<<SQL
DROP TABLE
`{$this->db->MimeElementSetLookup}`
SQL
);
    }
}

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
        // add the 'metadata' field to the File table
        $this->db->query("ALTER TABLE `{$this->db->File}` ADD `metadata` text collate utf8_unicode_ci NOT NULL");     
        
        // get the mime element set table name, but we cannot use $this->db->MimeElementSetLookup 
        // because the table was not correctly named with an 's' at the end.
        $mimeElementSetTableName = $this->db->prefix . 'mime_element_set_lookup'; 
        $this->db->query(<<<SQL
DROP TABLE
`{$mimeElementSetTableName}`
SQL
);
    }
}

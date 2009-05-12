<?php
class fileAuthenticationFix extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__24';
    
    // These may need to be changed if previous migrations change.
    const ELEMENT_AUTHENTICATION        = 'Authentication';
    const ELEMENT_SET_OMEKA_LEGACY_FILE = 'Omeka Legacy File';
    const RECORD_TYPE_FILE              = 'File';
    
    public function up()
    {
        $this->_backupTables();
        $this->_alterFiles();
        $this->_migrateAuthentication();
        $this->_deleteElementAndElementTexts();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    // Backup all tables that will be affected by this migration.
    private function _backupTables()
    {
        $db = $this->db;
        $sql = "
        CREATE TABLE `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}files`;
        INSERT `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}files`;
        
        CREATE TABLE `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}elements`;
        INSERT `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}elements`;
        
        CREATE TABLE `{$db->prefix}element_texts" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}element_texts`;
        INSERT `{$db->prefix}element_texts" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}element_texts`;";
        $db->execBlock($sql);
    }
    
    // Revert all tables that were affected by this migration.
    private function _revertTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}files`;
        RENAME TABLE `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}files`;
        
        DROP TABLE IF EXISTS `{$db->prefix}elements`;
        RENAME TABLE `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}elements`;
        
        DROP TABLE IF EXISTS `{$db->prefix}element_texts`;
        RENAME TABLE `{$db->prefix}element_texts" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}element_texts`;";
        $db->execBlock($sql);
    }
    
    private function _alterFiles()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}files` 
        ADD `authentication` CHAR(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL AFTER `has_derivative_image`";
        $db->exec($sql);
    }
    
    private function _migrateAuthentication()
    {
        $db = $this->db;
        $sql = "
        -- These joins are needed to ensure the right 'Authentication' element 
        -- is selected (in case the user added a custom 'Authentication' 
        -- element).
        UPDATE `{$db->prefix}files` f 
        JOIN `{$db->prefix}element_texts` et
            ON f.`id` = et.`record_id` 
        JOIN `{$db->prefix}record_types` rt 
            ON et.`record_type_id` = rt.`id` 
        JOIN `{$db->prefix}elements` e 
            ON et.`element_id` = e.`id` 
        JOIN `{$db->prefix}element_sets` es
            ON e.`element_set_id` = es.`id` 
        SET f.`authentication` = et.`text` 
        WHERE rt.`name` = '" . self::RECORD_TYPE_FILE . "' 
        AND e.`name` = '" . self::ELEMENT_AUTHENTICATION . "' 
        AND es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_FILE . "'";
        $db->exec($sql);
    }
    
    private function _deleteElementAndElementTexts()
    {
        $db = $this->db;
        $sql = "
        -- Delete both the 'Authentication' element and the element texts 
        -- assigned to the 'Authentication' element
        DELETE e, et 
        FROM `{$db->prefix}element_texts` et 
        JOIN `{$db->prefix}record_types` rt 
            ON et.`record_type_id` = rt.`id` 
        JOIN `{$db->prefix}elements` e 
            ON et.`element_id` = e.`id` 
        JOIN `{$db->prefix}element_sets` es
            ON e.`element_set_id` = es.`id` 
        WHERE rt.`name` = '" . self::RECORD_TYPE_FILE . "' 
        AND e.`name` = '" . self::ELEMENT_AUTHENTICATION . "' 
        AND es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_FILE . "'";
        $db->exec($sql);
    }
}

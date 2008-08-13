<?php
class addItemTypesElementsOrder extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__26';

    public function up()
    {
        $this->_backupTables();
        $this->_alterItemTypesElements();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    private function _backupTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}item_types_elements" . self::BACKUP_TABLE_SUFFIX . "`;
        CREATE TABLE `{$db->prefix}item_types_elements" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}item_types_elements`;
        INSERT `{$db->prefix}item_types_elements" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}item_types_elements`;
        ";
        $db->execBlock($sql);
    }
    
    private function _revertTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}item_types_elements`;
        RENAME TABLE `{$db->prefix}item_types_elements" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}item_types_elements`;
        ";
        $db->execBlock($sql);
    }
    
    private function _alterItemTypesElements()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}item_types_elements` 
        ADD `order` INT UNSIGNED NULL";
        $db->exec($sql);
    }
}

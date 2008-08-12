<?php
// This migration inserts an 'Item Type' element set that distinguishes elements 
// assigned to item types from 'floating' elements (i.e. those not assigned to 
// an item type or a specific element set). Two examples of floating elements 
// are 1) those created by administrators in the 'Item Types' page and not 
// assigned to an item type, and 2) those created by plugin writers and not 
// assigned to item types or an element set.
class itemTypeElementSet extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__25';
    
    const ELEMENT_SET_ITEM_TYPE = 'Item Type';
    
    const ELEMENT_SET_GENERIC_ITEM = 'Generic Item';
    
    const RECORD_TYPE_ITEM = 'Item';
    
    private $_elementSetItemTypeId;
    
    public function up()
    {
        $this->_backupTables();
        $this->_insertElementSet();
        $this->_updateElementSet();
        $this->_updateElements();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    private function _backupTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}element_sets" . self::BACKUP_TABLE_SUFFIX . "`;
        CREATE TABLE `{$db->prefix}element_sets" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}element_sets`;
        INSERT `{$db->prefix}element_sets" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}element_sets`;
        
        DROP TABLE IF EXISTS `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "`;
        CREATE TABLE `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}elements`;
        INSERT `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}elements`;        
        ";
        $db->execBlock($sql);
    }
    
    private function _revertTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}element_sets`;
        RENAME TABLE `{$db->prefix}element_sets" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}element_sets`;
        
        DROP TABLE IF EXISTS `{$db->prefix}elements`;
        RENAME TABLE `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}elements`;
        ";
        $db->execBlock($sql);
    }
    
    private function _insertElementSet()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO `{$db->prefix}element_sets` (
            `record_type_id`, 
            `name`, 
            `description`
        ) VALUES (
            (SELECT rt.`id` 
             FROM `{$db->prefix}record_types` rt 
             WHERE rt.`name` = '" . self::RECORD_TYPE_ITEM . "'), 
             'Item Type', 
             'The item type metadata element set, consisting of all item type elements bundled with Omeka and all item type elements created by an administrator.'
        );";
        $db->exec($sql);
        $this->_elementSetItemTypeId = $db->lastInsertId();
    }
    
    private function _updateElementSet()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}element_sets` 
        SET `description` = 'The generic item metadata element set, consisting of all item elements created by an administrator and not assigned to an item type, and elements created by plugins and not assigned to an item type or element set.' 
        WHERE `name` = '" . self::ELEMENT_SET_GENERIC_ITEM . "'";
        $db->exec($sql);
    }
    
    private function _updateElements()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}elements` e 
        JOIN `{$db->prefix}element_sets` es 
        ON e.`element_set_id` = es.`id` 
        -- Join on item_types_elements to only include elements belonging to an 
        -- item type (i.e. not 'floating' elements).
        JOIN `{$db->prefix}item_types_elements` ite 
        ON ite.`element_id` = e.`id` 
        SET e.`element_set_id` = {$this->_elementSetItemTypeId} 
        WHERE es.`name` = '" . self::ELEMENT_SET_GENERIC_ITEM . "';";
        $db->exec($sql);
    }
}

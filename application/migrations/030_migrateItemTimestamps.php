<?php
// This migration adds `added` and `modified` fields to the `items` table and 
// populates them with the appropriate timestamps from the `entities_relations` 
// table.
class migrateItemTimestamps extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__30';
    
    const ENTITIES_RELATIONS_TYPE_ITEM  = 'Item';
    const ENTITY_RELATIONSHIPS_ADDED    = 'added';
    const ENTITY_RELATIONSHIPS_MODIFIED = 'modified';
    
    public function up()
    {
        $this->_backupTables();
        $this->_alterItems(); 
        $this->_migrateItemAdded();
        $this->_migrateItemModified();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    private function _backupTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}items" . self::BACKUP_TABLE_SUFFIX . "`;
        CREATE TABLE `{$db->prefix}items" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}items`;
        INSERT `{$db->prefix}items" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}items`;
        ";
        $db->execBlock($sql);
    }
    
    private function _revertTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}items`;
        RENAME TABLE `{$db->prefix}items" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}items`;
        ";
        $db->execBlock($sql);
    }
    
    private function _alterItems()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}items` 
        ADD `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
        ADD `added` timestamp NOT NULL default '0000-00-00 00:00:00'";
        $db->exec($sql);
    }
    
    private function _migrateItemAdded()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}items` i 
        SET i.`added` = (
            SELECT er.`time` 
            FROM `{$db->prefix}entities_relations` er 
            WHERE er.`relation_id` = i.`id` 
            AND er.`type` = '" . self::ENTITIES_RELATIONS_TYPE_ITEM . "' 
            AND er.`relationship_id` = (
                SELECT ers.`id` 
                FROM `{$db->prefix}entity_relationships` ers 
                WHERE ers.`name` = '".self::ENTITY_RELATIONSHIPS_ADDED."' 
            )
            ORDER BY er.`time` ASC 
            LIMIT 1
        ), 
        i.`modified` = '0000-00-00 00:00:00'";
        $db->exec($sql);
    }
    
    private function _migrateItemModified()
    {
        $db = $this->db;
        /*
        // This doesn't work because multi-table syntax does not allow ORDER BY
        // or LIMIT
        $sql = "
        UPDATE `{$db->prefix}items` i, 
        `{$db->prefix}entities_relations` er 
        SET i.`modified` = er.`time` 
        WHERE i.`id` = er.`relation_id` 
        AND er.`type` = 'Item' 
        ORDER BY er.`time` DESC 
        LIMIT 1";
        */
        $sql = "
        UPDATE `{$db->prefix}items` i 
        SET i.`modified` = (
            SELECT er.`time` 
            FROM `{$db->prefix}entities_relations` er 
            WHERE er.`relation_id` = i.`id` 
            AND er.`type` = '" . self::ENTITIES_RELATIONS_TYPE_ITEM . "' 
            AND er.`relationship_id` = (
                SELECT ers.`id` 
                FROM `{$db->prefix}entity_relationships` ers 
                WHERE ers.`name` = '".self::ENTITY_RELATIONSHIPS_MODIFIED."' 
            )
            ORDER BY er.`time` DESC 
            LIMIT 1
        )";
        $db->exec($sql);
    }
}

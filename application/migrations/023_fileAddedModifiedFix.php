<?php
class fileAddedModifiedFix extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__23';
    
    public function up()
    {
        $this->_backupTables();
        $this->_alter();
        $this->_migrate();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    private function _backupTables()
    {
        $db = $this->db;
        $sql = "
        CREATE TABLE `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}files`;
        INSERT `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}files`;";
        $db->execBlock($sql);
    }
    
    private function _revertTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}files`;
        RENAME TABLE `{$db->prefix}files" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}files`;";
        $db->execBlock($sql);
    }
    
    private function _alter()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}files` 
        ADD `modified` TIMESTAMP NOT NULL, 
        ADD `added` TIMESTAMP NOT NULL";
        $db->exec($sql);
    }
    
    private function _migrate()
    {
        $db = $this->db;
        
        // Return if the backup table doesn't exist.
        if (!in_array("{$db->prefix}files__backup__21", $db->listTables())) {
            return;
        }
        
        $sql = "
        UPDATE `{$db->prefix}files` f, `{$db->prefix}files__backup__21` fb
        SET f.`modified` = fb.`modified`, 
        f.`added` = fb.`added` 
        WHERE f.`id` = fb.`id`";
        $db->exec($sql);
    }
}

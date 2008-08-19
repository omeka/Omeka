<?php
class deleteDataTypes extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__27';
    
    private $_dataTypeNames = array('Floating Point', 
                                    'Boolean', 
                                    'Time', 
                                    'Time Range', 
                                    'Date Time Range');

    public function up()
    {
        $this->_backupTables();
        $this->_deleteDataTypes();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    private function _backupTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}data_types" . self::BACKUP_TABLE_SUFFIX . "`;
        CREATE TABLE `{$db->prefix}data_types" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}data_types`;
        INSERT `{$db->prefix}data_types" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}data_types`;
        ";
        $db->execBlock($sql);
    }
    
    private function _revertTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}data_types`;
        RENAME TABLE `{$db->prefix}data_types" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}data_types`;
        ";
        $db->execBlock($sql);
    }
    
    private function _deleteDataTypes()
    {
        $db = $this->db;
        $sql = "
        DELETE FROM `{$db->prefix}data_types` 
        WHERE `name` IN (\"" . implode('", "', $this->_dataTypeNames) . "\")";
        $db->exec($sql);
    }
}

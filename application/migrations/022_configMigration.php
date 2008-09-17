<?php
class configMigration extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__22';
    
    private $_config;
    
    public function up()
    {
        $this->_backupTables();
        $this->_setConfig();
        
        $this->_insertConfig();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    private function _backupTables()
    {
        $db = $this->db;
        $sql = "
        CREATE TABLE `{$db->prefix}options" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}options`;
        INSERT `{$db->prefix}options" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}options`;";
        $db->execBlock($sql);
    }
    
    private function _revertTables()
    {
        $db = $this->db;
        $sql = "
        DROP TABLE IF EXISTS `{$db->prefix}options`;
        RENAME TABLE `{$db->prefix}options" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}options`;";
        $db->execBlock($sql);
    }
    
    private function _setConfig()
    {
        require_once 'Zend/Config/Ini.php';
        $config = new Zend_Config_Ini(CONFIG_DIR 
                                    . DIRECTORY_SEPARATOR 
                                    . 'config.ini', 'site');
        if (@isset($config->pagination->per_page)) {
            $perPage = $config->pagination->per_page;
        } else {
            $perPage = 10;
        }
        // add more configs here if needed.
        $this->_config = array(
            array('per_page_admin', $perPage), 
            array('per_page_public', $perPage)
        );
    }
    
    private function _insertConfig()
    {
        $db = $this->db;
        $sql = "
        INSERT INTO {$db->prefix}options (
            name, 
            value
        ) VALUES (?, ?)";
        
        foreach ($this->_config as $config) {
            $db->exec($sql, $config);
        }
    }
}

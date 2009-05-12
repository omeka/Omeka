<?php
class deleteBackupTables extends Omeka_Db_Migration
{
    private $_backupTables = array('data_types__backup__27', 
                                   'elements__backup__24', 
                                   'elements__backup__25', 
                                   'elements__backup__28', 
                                   'elements__backup__29', 
                                   'element_sets__backup__25', 
                                   'element_sets__backup__29', 
                                   'element_texts__backup__24', 
                                   'element_texts__backup__29', 
                                   'files_images__backup__21', 
                                   'files_videos__backup__21', 
                                   'files__backup__21', 
                                   'files__backup__23', 
                                   'files__backup__24', 
                                   'file_meta_lookup__backup__21', 
                                   'items__backup__19', 
                                   'items__backup__30', 
                                   'item_types_elements__backup__26', 
                                   'metafields__backup__19', 
                                   'metatext__backup__19', 
                                   'options__backup__22', 
                                   'types_metafields__backup__19', 
                                   'types__backup__19');
    
    public function up()
    {
        $db = $this->db;
        foreach ($this->_backupTables as $backupTable) {
            $sql = "DROP TABLE IF EXISTS `{$db->prefix}{$backupTable}`";
            $db->query($sql);
        }
    }
}
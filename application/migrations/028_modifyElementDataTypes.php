<?php
class modifyElementDataTypes extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__28';
    
    const ELEMENT_SET_DUBLIN_CORE       = 'Dublin Core';
    const ELEMENT_SET_OMEKA_LEGACY_ITEM = 'Omeka Legacy Item';
    const ELEMENT_SET_ITEM_TYPE         = 'Item Type';
    
    const DATA_TYPE_TEXT       = 'Text';
    const DATA_TYPE_TINY_TEXT  = 'Tiny Text';
    const DATA_TYPE_DATE       = 'Date';
    const DATA_TYPE_DATE_RANGE = 'Date Range';
    const DATA_TYPE_INTEGER    = 'Integer';
    const DATA_TYPE_DATE_TIME  = 'Date Time';
    
    private $_elements = array(
        // *********************************************************************
        // Dublin Core element set
        array('element_name'     => 'Contributor', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Coverage', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Creator', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Format', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Identifier', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Language', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Publisher', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Relation', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Rights', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Source', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Subject', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Title', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Type', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Description', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Date', 
              'element_set_name' => self::ELEMENT_SET_DUBLIN_CORE, 
              'data_type_name'   => self::DATA_TYPE_DATE), 
        // *********************************************************************
        // Omeka Legacy Item element set
        array('element_name'     => 'Additional Creator', 
              'element_set_name' => self::ELEMENT_SET_OMEKA_LEGACY_ITEM, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Rights Holder', 
              'element_set_name' => self::ELEMENT_SET_OMEKA_LEGACY_ITEM, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Spatial Coverage', 
              'element_set_name' => self::ELEMENT_SET_OMEKA_LEGACY_ITEM, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Citation', 
              'element_set_name' => self::ELEMENT_SET_OMEKA_LEGACY_ITEM, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Provenance', 
              'element_set_name' => self::ELEMENT_SET_OMEKA_LEGACY_ITEM, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Temporal Coverage', 
              'element_set_name' => self::ELEMENT_SET_OMEKA_LEGACY_ITEM, 
              'data_type_name'   => self::DATA_TYPE_DATE_RANGE), 
        // *********************************************************************
        // Item Type element set
        array('element_name'     => 'Text', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Interviewer', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Interviewee', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Location', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Transcription', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Local URL', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Original Format', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Physical Dimensions', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Duration', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Compression', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Producer', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Director', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Bit Rate/Frequency', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Time Summary', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Email Body', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Subject Line', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'From', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'To', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'CC', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'BCC', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Number of Attachments', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Standards', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Objectives', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Materials', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Lesson Plan Text', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'URL', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Event Type', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Participants', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Birth Date', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_DATE), 
        array('element_name'     => 'Birthplace', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Death Date', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_DATE), 
        array('element_name'     => 'Occupation', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TINY_TEXT), 
        array('element_name'     => 'Biographical Text', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
        array('element_name'     => 'Bibliography', 
              'element_set_name' => self::ELEMENT_SET_ITEM_TYPE, 
              'data_type_name'   => self::DATA_TYPE_TEXT), 
    );
    
    public function up()
    {
        $this->_backupTables();
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
        DROP TABLE IF EXISTS `{$db->prefix}elements`;
        RENAME TABLE `{$db->prefix}elements" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}elements`;
        ";
        $db->execBlock($sql);
    }
    
    private function _updateElements()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}elements` e
        SET e.`data_type_id` = (
            SELECT dt.`id` 
            FROM `{$db->prefix}data_types` dt
            WHERE dt.`name` = ?
        ) 
        WHERE e.`element_set_id` = (
            SELECT es.`id` 
            FROM `{$db->prefix}element_sets` es 
            WHERE es.`name` = ?
        )
        AND e.`name` = ?";
        foreach ($this->_elements as $element) {
            $dataTypeName   = $element['data_type_name'];
            $elementSetName = $element['element_set_name'];
            $elementName    = $element['element_name'];
            $db->exec($sql, array($dataTypeName, $elementSetName, $elementName));
        }
    }
}

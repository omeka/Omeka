<?php
class DcRewriteFix extends Omeka_Db_Migration
{
    // New record types
    // If this changes, you may need to modify the SQL in:
    //     $this->_updateElementTexts()
    //     $this->_updateElements()
    //     $this->_updateElementSets()
    private $_rt = array(
        array('name' => 'All', 
              'description' => 'Elements, element sets, and element texts assigned to this record type relate to all possible records.'), 
        array('name' => 'Item', 
              'description' => 'Elements, element sets, and element texts assigned to this record type relate to item records.'), 
    );
    
    // New data types
    private $_dt = array(
        array('name' => 'Integer', 
              'description' => 'Set of numbers consisting of the natural numbers including 0 (0, 1, 2, 3, ...) and their negatives (0, −1, −2, −3, ...).'), 
        array('name' => 'Floating Point', 
              'description' => 'A number with a specified number decimal places, or fractional part.'), 
        array('name' => 'Boolean', 
              'description' => 'A primitive datatype having one of two values: true or false, off or on, 1 or 0.'), 
        array('name' => 'Time', 
              'description' => 'A time in format hh:mm:ss'), 
        array('name' => 'Time Range', 
              'description' => 'A time range in format hh:mm:ss hh:mm:ss'), 
        array('name' => 'Date', 
              'description' => 'A date in format yyyy-mm-dd'), 
        array('name' => 'Date Time', 
              'description' => 'A date and time combination in the format: yyyy-mm-dd hh:mm:ss'), 
        array('name' => 'Date Time Range', 
              'description' => 'A date and time combination range, begin to end. In format yyyy-mm-dd hh:mm:ss yyyy-mm-dd hh:mm:ss'), 
    );
    
    // New element sets
    // If this changes, you may need to modify the SQL in:
    //     $this->_updateElements()
    //     $this->_updateElementSets()
    private $_es = array(
        array('name' => 'Generic Item', 
              'description' => 'The generic item metadata element set, consisting of all item elements bundled with Omeka and all item elements created by an administrator and not assigned to a particular element set.'), 
    );
    
    // The method call order is very important here.
    public function up()
    {
        // record_types
        $this->_createRecordTypes();
        $this->_insertRecordTypes();
        
        // data_types
        $this->_renameElementTypes();
        $this->_alterDataTypes();
        $this->_updateDataTypes();
        $this->_insertDataTypes();
        
        // element_sets
        $this->_alterElementSets();
        $this->_insertElementSets();
        $this->_updateElementSets();
        
        // elements
        $this->_alterElements();
        $this->_updateElements();
        
        // element_texts
        $this->_renameItemsElements();
        $this->_alterElementTexts();
        $this->_updateElementTexts();
        
        // items
        $this->_alterItems();
        
        // item_types
        $this->_alterItemTypes();
        
        // item_types_elements
        $this->_alterItemTypesElements();
    }
    
    public function down()
    {
    }
    
    /**************************************************************************/
    // record_types
    
    private function _createRecordTypes()
    {
        $db = $this->db;
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$db->prefix}record_types` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `name` varchar(255) collate utf8_unicode_ci NOT NULL,
            `description` text collate utf8_unicode_ci,
            PRIMARY KEY  (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->exec($sql);
    }
    
    private function _insertRecordTypes()
    {
        $db = $this->db;
        $sql = "INSERT INTO `{$db->prefix}record_types` (
            `name`, 
            `description`
        ) VALUES (?, ?)";
        foreach ($this->_rt as $recordType) {
            $name        = $recordType['name'];
            $description = $recordType['description'];
            $db->exec($sql, array($name, $description));
        }
    }

    /**************************************************************************/
    // data_types
    
    private function _renameElementTypes()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE `{$db->prefix}element_types` 
        TO `{$db->prefix}data_types`;";
        $db->exec($sql);
    }
    
    private function _alterDataTypes()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}data_types` 
        ADD UNIQUE `name` (`name`);
        
        ALTER TABLE `{$db->prefix}data_types` 
        DROP `regular_expression`;
        
        ALTER TABLE `{$db->prefix}data_types` 
        MODIFY `name` varchar(255) collate utf8_unicode_ci NOT NULL;";
        $db->execBlock($sql);
    }
    
    private function _updateDataTypes()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}data_types` 
        SET `name` = 'Text', 
        `description` = 'A long, typically multi-line text string. Up to 65535 characters.' 
        WHERE `name` = 'text';
        
        UPDATE `{$db->prefix}data_types` 
        SET `name` = 'Tiny Text', 
        `description` = 'A short, typically one-line text string. Up to 255 characters.' 
        WHERE `name` = 'tinytext';
        
        UPDATE `{$db->prefix}data_types` 
        SET `name` = 'Date Range', 
        `description` = 'A date range, begin to end. In format yyyy-mm-dd yyyy-mm-dd.' 
        WHERE `name` = 'daterange';";
        $db->execBlock($sql);
    }
    
    private function _insertDataTypes()
    {
        $db = $this->db;
        $sql = "INSERT INTO `{$db->prefix}data_types` (
            `name`, 
            `description`
        ) VALUES (?, ?)";
        foreach ($this->_dt as $dataType) {
            $name        = $dataType['name'];
            $description = $dataType['description'];
            $db->exec($sql, array($name, $description));
        }
    }
    
    /**************************************************************************/
    // element_sets
        
    private function _alterElementSets()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}element_sets` 
        ADD UNIQUE `name` (`name`);
        
        ALTER TABLE `{$db->prefix}element_sets` 
        ADD `record_type_id` int(10) unsigned NOT NULL AFTER `id`;
        
        ALTER TABLE `{$db->prefix}element_sets` 
        ADD INDEX `record_type_id` (`record_type_id`);
        
        ALTER TABLE `{$db->prefix}element_sets` 
        MODIFY `name` varchar(255) collate utf8_unicode_ci NOT NULL;";
        $db->execBlock($sql);
    }
    
    private function _insertElementSets()
    {
        $db = $this->db;
        $sql = "INSERT INTO `{$db->prefix}element_sets` (
            `name`, 
            `description`
        ) VALUES (?, ?)";
        foreach ($this->_es as $elementSet) {
            $name        = $elementSet['name'];
            $description = $elementSet['description'];
            $db->exec($sql, array($name, $description));
        }
    }
    
     private function _updateElementSets()
    {
        $db = $this->db;
        $sql = "
        UPDATE `{$db->prefix}element_sets` 
        SET `name` = 'Omeka Legacy Item' 
        WHERE `name` = 'Omeka Legacy Elements';
        
        UPDATE `{$db->prefix}element_sets` 
        SET `name` = 'Dublin Core' 
        WHERE `name` = 'Dublin Core Metadata Element Set';
        
        -- Set the 'All' record type ID for element sets in the 'Dublin Core' 
        -- element set.
        UPDATE `{$db->prefix}element_sets` es 
        JOIN `{$db->prefix}record_types` rt 
        SET es.`record_type_id` = rt.`id` 
        WHERE es.`name` = 'Dublin Core' 
        AND rt.`name` = 'All';
        
        -- Set the 'Item' record type ID for element sets in the 'Generic Item' 
        -- and 'Omeka Legacy Item' element sets.
        UPDATE `{$db->prefix}element_sets` es 
        JOIN `{$db->prefix}record_types` rt 
        SET es.`record_type_id` = rt.`id` 
        WHERE (
            es.`name` = 'Omeka Legacy Item' 
            OR es.`name` = 'Generic Item'
        ) 
        AND rt.`name` = 'Item';";
        $db->execBlock($sql);
    }
    
    /**************************************************************************/
    // elements
    
    private function _alterElements()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}elements` 
        MODIFY `name` varchar(255) collate utf8_unicode_ci NOT NULL;
        
        ALTER TABLE `{$db->prefix}elements` 
        DROP `plugin_id`;
        
        ALTER TABLE `{$db->prefix}elements` 
        CHANGE `element_type_id` `data_type_id` int(10) unsigned NOT NULL;
        
        ALTER TABLE `{$db->prefix}elements` 
        ADD `record_type_id` int(10) unsigned NOT NULL AFTER `id`;
        
        ALTER TABLE `{$db->prefix}elements` 
        ADD UNIQUE `name_element_set_id` (`element_set_id`, `name`);
        
        ALTER TABLE `{$db->prefix}elements` 
        ADD INDEX `record_type_id` (`record_type_id`);
        
        ALTER TABLE `{$db->prefix}elements` 
        ADD INDEX `data_type_id` (`data_type_id`);
        
        ALTER TABLE `{$db->prefix}elements` 
        ADD INDEX `element_set_id` (`element_set_id`);
        
        ALTER TABLE `{$db->prefix}elements` 
        MODIFY `element_set_id` int(10) unsigned NOT NULL;
        
        ALTER TABLE `{$db->prefix}elements` 
        MODIFY `order` int(10) unsigned default NULL AFTER `element_set_id`;
        
        ALTER TABLE `{$db->prefix}elements` 
        ADD UNIQUE `order_element_set_id` (`element_set_id`,`order`) ";
        $db->execBlock($sql);
    }
    
    private function _updateElements()
    {
        $db = $this->db;
        $sql = "
        -- Set the element set ID for elements in the generic element set.
        UPDATE `{$db->prefix}elements` e 
        JOIN `{$db->prefix}element_sets` es 
        SET e.`element_set_id` = es.`id` 
        -- e.`element_set_id` was cast from NULL to 0 in _alterElements().
        WHERE e.`element_set_id` = 0 
        AND es.`name` = 'Generic Item';
        
        -- Set the 'Item' record type ID for elements in the 'Generic Item' and 
        -- 'Omeka Legacy Item' element sets.
        UPDATE `{$db->prefix}elements` e 
        JOIN `{$db->prefix}element_sets` es 
        JOIN `{$db->prefix}record_types` rt 
        SET e.`record_type_id` = rt.`id` 
        WHERE e.`element_set_id` = es.`id` 
        AND (
            es.`name` = 'Generic Item' 
            OR es.`name` = 'Omeka Legacy Item'
        )
        AND rt.`name` = 'Item';
        
        -- Set the 'All' record type ID for elements in the 'Dublin Core' 
        -- element set.
        UPDATE `{$db->prefix}elements` e 
        JOIN `{$db->prefix}element_sets` es 
        JOIN `{$db->prefix}record_types` rt 
        SET e.`record_type_id` = rt.`id` 
        WHERE e.`element_set_id` = es.`id` 
        AND es.`name` = 'Dublin Core' 
        AND rt.`name` = 'All';";
        $db->execBlock($sql);
    }
    
    /**************************************************************************/
    // element_texts
    
    private function _renameItemsElements()
    {
        $db = $this->db;
        $sql = "
        RENAME TABLE `{$db->prefix}items_elements` 
        TO `{$db->prefix}element_texts`;";
        $db->exec($sql);
    }
    
    private function _alterElementTexts()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}element_texts` 
        CHANGE `item_id` `record_id` int(10) unsigned NOT NULL;
        
        ALTER TABLE `{$db->prefix}element_texts` 
        ADD `record_type_id` int(10) unsigned NOT NULL AFTER `record_id`;
        
        ALTER TABLE `{$db->prefix}element_texts` 
        ADD `html` tinyint(1) NOT NULL AFTER `element_id`;
        
        ALTER TABLE `{$db->prefix}element_texts` 
        ADD INDEX `record_id` (`record_id`);
        
        ALTER TABLE `{$db->prefix}element_texts` 
        ADD INDEX `record_type_id` (`record_type_id`);
        
        ALTER TABLE `{$db->prefix}element_texts` 
        ADD INDEX `element_id` (`element_id`);
        
        ALTER TABLE `{$db->prefix}element_texts` 
        ADD FULLTEXT `text` (`text`);
        
        ALTER TABLE `{$db->prefix}element_texts` 
        MODIFY `text` mediumtext collate utf8_unicode_ci NOT NULL;";
        $db->execBlock($sql);
    }
    
    private function _updateElementTexts()
    {
        $db = $this->db;
        $sql = "
        -- Set the 'Item' record type ID for all element texts. This is because 
        -- only item metadata is stored in `element_texts` up to this point.
        UPDATE `{$db->prefix}element_texts` et 
        JOIN `{$db->prefix}record_types` rt 
        SET et.`record_type_id` = rt.`id` 
        WHERE rt.`name` = 'Item';";
        $db->execBlock($sql);
    }
    
    /**************************************************************************/
    // items
    
    private function _alterItems()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}items` 
        ADD INDEX `item_type_id` (`item_type_id`);
        
        ALTER TABLE `{$db->prefix}items` 
        ADD INDEX `collection_id` (`collection_id`);";
        $db->execBlock($sql);
    }
    
    /**************************************************************************/
    // item_types
    
    private function _alterItemTypes()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}item_types` 
        DROP `plugin_id`;
        
        ALTER TABLE `{$db->prefix}item_types` 
        MODIFY `name` varchar(255) collate utf8_unicode_ci NOT NULL;
        
        ALTER TABLE `{$db->prefix}item_types` 
        ADD UNIQUE `name` (`name`);";
        $db->execBlock($sql);
    }
    
    /**************************************************************************/
    // item_types_elements
    
    private function _alterItemTypesElements()
    {
        $db = $this->db;
        $sql = "
        ALTER TABLE `{$db->prefix}item_types_elements` 
        DROP `plugin_id`;
        
        ALTER TABLE `{$db->prefix}item_types_elements` 
        ADD UNIQUE `item_type_id_element_id` (`item_type_id`, `element_id`);
        
        ALTER TABLE `{$db->prefix}item_types_elements` 
        ADD INDEX `item_type_id` (`item_type_id`);
        
        ALTER TABLE `{$db->prefix}item_types_elements` 
        ADD INDEX `element_id` (`element_id`);";
        $db->execBlock($sql);
    }
 }

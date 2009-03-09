<?php
// In effect, we're merging the "Generic Item" and "Omeka Legacy Item" element 
// sets into one element set named "Additional Item Metadata". This is more 
// complicated that it sounds.
class fixElementSets extends Omeka_Db_Migration
{
    const BACKUP_TABLE_SUFFIX = '__backup__29';
    
    const ELEMENT_SET_DUBLIN_CORE = 'Dublin Core';
    const ELEMENT_SET_GENERIC_ITEM = 'Generic Item';
    const ELEMENT_SET_OMEKA_LEGACY_ITEM = 'Omeka Legacy Item';
    const ELEMENT_SET_ADDITIONAL_ITEM_METADATA = 'Additional Item Metadata';
    const ELEMENT_SET_ITEM_TYPE = 'Item Type';
    const ELEMENT_SET_ITEM_TYPE_METADATA = 'Item Type Metadata';
    
    const ELEMENT_ADDITIONAL_CREATOR = 'Additional Creator';
    const ELEMENT_CREATOR = 'Creator';
    
    public function up()
    {
        $this->_backupTables();
        $this->_updateElementSets();
        $this->_migrateAdditionalCreator();
        $this->_updateElements();
    }
    
    public function down()
    {
        $this->_revertTables();
    }
    
    // Tables that will be affected: `element_sets`, `elements`, `element_texts`
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
        
        DROP TABLE IF EXISTS `{$db->prefix}element_texts" . self::BACKUP_TABLE_SUFFIX . "`;
        CREATE TABLE `{$db->prefix}element_texts" . self::BACKUP_TABLE_SUFFIX . "` LIKE `{$db->prefix}element_texts`;
        INSERT `{$db->prefix}element_texts" . self::BACKUP_TABLE_SUFFIX . "` SELECT * FROM `{$db->prefix}element_texts`;
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
        
        DROP TABLE IF EXISTS `{$db->prefix}element_texts`;
        RENAME TABLE `{$db->prefix}element_texts" . self::BACKUP_TABLE_SUFFIX . "` TO `{$db->prefix}element_texts`;
        ";
        $db->execBlock($sql);
    }
    
    // Perform any updates needed in the `element_sets` table.
    private function _updateElementSets()
    {
        $db = $this->db;
        
        // Change the name of the "Generic Item" element set to "Additional Item 
        // Metadata".
        $sql = "
        UPDATE `{$db->prefix}element_sets` 
        SET `name` = '" . self::ELEMENT_SET_ADDITIONAL_ITEM_METADATA . "', 
        `description` = 'The additional item metadata element set, consisting of all item elements created by an administrator and not assigned to an item type, and item elements created by plugins and not assigned to an item type or other element set. Additionally, the metadata element set that, in addition to the Dublin Core element set, was included in the `items` table in previous versions of Omeka. These elements are common to all Omeka items.' 
        WHERE `name` = '" . self::ELEMENT_SET_GENERIC_ITEM . "'";
        $db->exec($sql);
        
        // Now change the name of the "Item Type" element set to "Item Type 
        // Metadata". This is a minor change that has no bearing on the rest of 
        // this migration.
        $sql = "
        UPDATE `{$db->prefix}element_sets` 
        SET `name` = '" . self::ELEMENT_SET_ITEM_TYPE_METADATA . "' 
        WHERE `name` = '" . self::ELEMENT_SET_ITEM_TYPE . "'";
        $db->exec($sql);
    }
    
    // Migrate element texts assigned to the "Omeka Legacy Item"."Additional 
    // Creator" element to the "Dublin Core"."Creator" element.
    private function _migrateAdditionalCreator()
    {
        $db = $this->db;
        
        // Migrate element texts assigned to the "Additional Creator" element to 
        // the already existing "Creator" element.
        $sql = "
        UPDATE `{$db->prefix}element_texts` et
        SET et.`element_id` = (
            SELECT e.`id` 
            FROM `{$db->prefix}elements` e 
            WHERE e.`name` = '" . self::ELEMENT_CREATOR . "' 
            AND e.`element_set_id` = (
                SELECT es.`id` 
                FROM `{$db->prefix}element_sets` es 
                WHERE es.`name` = '" . self::ELEMENT_SET_DUBLIN_CORE . "'
            )
        ) 
        WHERE et.`element_id` = (
            SELECT e.`id` 
            FROM `{$db->prefix}elements` e 
            WHERE e.`name` = '" . self::ELEMENT_ADDITIONAL_CREATOR . "'
            AND e.`element_set_id` = (
                SELECT es.`id` 
                FROM `{$db->prefix}element_sets` es 
                WHERE es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_ITEM . "'
            )
        )";
        $db->exec($sql);
        
        // Now delete the obsolete "Additional Creator" element.
        /*
        // This doesn't work because apparently you can't use subqueries in 
        // delete statements, even though MySQL documentation only says deleting 
        // and selecting FROM THE SAME TABLE is not allowed, and that doesn't 
        // apply to this statement...
        // So, it appears that DELETE statements cannot have correlations (table 
        // aliases) unless there is more than one table in the FROM clause. This 
        // is undocumented... 
        // No it's not: From the MySQL docs: "If you declare an alias for a 
        // table, you must use the alias when referring to the table." Who knew? 
        // So this would have probably worked if I had only added "e" 
        // immediately after DELETE.
        $sql = "
        DELETE FROM `{$db->prefix}elements` e 
        WHERE e.`name` = '" . self::ELEMENT_ADDITIONAL_CREATOR . "'
        AND e.`element_set_id` = (
            SELECT es.`id` 
            FROM `{$db->prefix}element_sets` es 
            WHERE es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_ITEM . "'
        )";
        */
        $sql = "
        DELETE e 
        FROM `{$db->prefix}elements` e 
        JOIN `{$db->prefix}element_sets` es 
        ON e.`element_set_id` = es.`id` 
        WHERE e.`name` = '" . self::ELEMENT_ADDITIONAL_CREATOR . "' 
        AND es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_ITEM . "'";
        $db->exec($sql);
    }
    
    // Move the elements assigned to the "Omeka Legacy Item" element set to the 
    // new "Additional Item Metadata" element set.
    private function _updateElements()
    {
        $db = $this->db;
        
        // First set the `order` of "Omeka Legacy Item" elements to NULL. This 
        // is because `elements` contains a duel unique field using `order` and 
        // `element_set_id`. We're not going to reset the `order` because there 
        // is no need, is there?
        $sql = "
        UPDATE `{$db->prefix}elements` e 
        SET e.`order` = NULL 
        WHERE e.`element_set_id` = (
            SELECT es.`id` 
            FROM `{$db->prefix}element_sets` es 
            WHERE es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_ITEM . "'
        )";
        $db->exec($sql);
        
        // Now Move the "Omeka Legacy Item" elements to the "Additional Item 
        // Metadata" element set.
        $sql = "
        UPDATE `{$db->prefix}elements` e 
        SET e.`element_set_id` = (
            SELECT es.`id` 
            FROM `{$db->prefix}element_sets` es 
            WHERE es.`name` = '" . self::ELEMENT_SET_ADDITIONAL_ITEM_METADATA . "'
        ) 
        WHERE e.`element_set_id` = (
            SELECT es.`id` 
            FROM `{$db->prefix}element_sets` es 
            WHERE es.`name` = '" . self::ELEMENT_SET_OMEKA_LEGACY_ITEM . "'
        )";
        $db->exec($sql);
        
        // Now delete the obsolete "Omeka Legacy Item" element set.
        $sql = "
        DELETE es FROM `{$db->prefix}element_sets` es 
        WHERE es.`name` = '". self::ELEMENT_SET_OMEKA_LEGACY_ITEM . "'";
        $db->exec($sql);
    }
}

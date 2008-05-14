<?php
class SchemaScrub extends Omeka_Db_Migration
{
    public function up()
    {
        $db = $this->db;
        $sql = "
        -- Alter character set
        ALTER TABLE `$db->Taggings` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
        
        -- Alter engine
        ALTER TABLE `$db->EntitiesRelations` ENGINE = MYISAM;
        ALTER TABLE `$db->EntityRelationships` ENGINE = MYISAM;
        ALTER TABLE `$db->FilesImages` ENGINE = MYISAM;
        
        -- Alter data types
        ALTER TABLE `$db->Collection` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE `$db->Entity` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            CHANGE `parent_id` `parent_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `$db->EntitiesRelations` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `entity_id` `entity_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `relation_id` `relation_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `relationship_id` `relationship_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `$db->EntityRelationships` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `$db->Exhibit` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE `$db->File` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `item_id` `item_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `size` `size` INT UNSIGNED NOT NULL DEFAULT '0',
            CHANGE `lookup_id` `lookup_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `$db->FilesImages` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `width` `width` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `height` `height` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `bit_depth` `bit_depth` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `channels` `channels` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `file_id` `file_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `$db->FilesVideos` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `bitrate` `bitrate` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `duration` `duration` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `sample_rate` `sample_rate` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `file_id` `file_id` INT UNSIGNED NOT NULL ,
            CHANGE `width` `width` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `height` `height` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `$db->FileMetaLookup` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE `$db->Item` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `type_id` `type_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `collection_id` `collection_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `$db->ExhibitPageEntry` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `item_id` `item_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `page_id` `page_id` INT UNSIGNED NOT NULL;
        ALTER TABLE `$db->Metafield` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `plugin_id` `plugin_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `$db->Metatext` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `item_id` `item_id` INT UNSIGNED NOT NULL ,
            CHANGE `metafield_id` `metafield_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `$db->Option` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `$db->Plugin` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `$db->ExhibitSection` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `exhibit_id` `exhibit_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `$db->ExhibitPage` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `section_id` `section_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `$db->Taggings` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `relation_id` `relation_id` INT UNSIGNED NOT NULL ,
            CHANGE `tag_id` `tag_id` INT UNSIGNED NOT NULL ,
            CHANGE `entity_id` `entity_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `$db->Tag` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `$db->Type` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `plugin_id` `plugin_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `$db->TypesMetafields` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `type_id` `type_id` INT UNSIGNED NOT NULL ,
            CHANGE `metafield_id` `metafield_id` INT UNSIGNED NOT NULL ,
            CHANGE `plugin_id` `plugin_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `$db->User` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `entity_id` `entity_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `$db->UsersActivations` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL; 
        ";
        $this->execBlock($sql);
    }
}

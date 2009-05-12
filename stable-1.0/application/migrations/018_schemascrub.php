<?php
class SchemaScrub extends Omeka_Db_Migration
{
    public function up()
    {
        $db = $this->db;
        $sql = "
        -- Alter character set
        ALTER TABLE `{$db->prefix}taggings` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
        
        -- Alter engine
        ALTER TABLE `{$db->prefix}entities_relations` ENGINE = MYISAM;
        ALTER TABLE `{$db->prefix}entity_relationships` ENGINE = MYISAM;
        ALTER TABLE `{$db->prefix}files_images` ENGINE = MYISAM;
        
        -- Alter data types
        ALTER TABLE `{$db->prefix}collections` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE `{$db->prefix}entities` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            CHANGE `parent_id` `parent_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `{$db->prefix}entities_relations` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `entity_id` `entity_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `relation_id` `relation_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `relationship_id` `relationship_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `{$db->prefix}entity_relationships` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `{$db->prefix}exhibits` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE `{$db->prefix}files` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `item_id` `item_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `size` `size` INT UNSIGNED NOT NULL DEFAULT '0',
            CHANGE `lookup_id` `lookup_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `{$db->prefix}files_images` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `width` `width` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `height` `height` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `bit_depth` `bit_depth` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `channels` `channels` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `file_id` `file_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `{$db->prefix}files_videos` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `bitrate` `bitrate` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `duration` `duration` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `sample_rate` `sample_rate` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `file_id` `file_id` INT UNSIGNED NOT NULL ,
            CHANGE `width` `width` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `height` `height` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `{$db->prefix}file_meta_lookup` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
        ALTER TABLE `{$db->prefix}items` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `type_id` `type_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `collection_id` `collection_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `{$db->prefix}items_section_pages` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `item_id` `item_id` INT UNSIGNED NULL DEFAULT NULL ,
            CHANGE `page_id` `page_id` INT UNSIGNED NOT NULL;
        ALTER TABLE `{$db->prefix}metafields` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `plugin_id` `plugin_id` INT UNSIGNED NULL DEFAULT NULL;
        ALTER TABLE `{$db->prefix}metatext` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `item_id` `item_id` INT UNSIGNED NOT NULL ,
            CHANGE `metafield_id` `metafield_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `{$db->prefix}options` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `{$db->prefix}plugins` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `{$db->prefix}sections` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `exhibit_id` `exhibit_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `{$db->prefix}section_pages` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `section_id` `section_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `{$db->prefix}taggings` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `relation_id` `relation_id` INT UNSIGNED NOT NULL ,
            CHANGE `tag_id` `tag_id` INT UNSIGNED NOT NULL ,
            CHANGE `entity_id` `entity_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `{$db->prefix}tags` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;  
        ALTER TABLE `{$db->prefix}types` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `plugin_id` `plugin_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `{$db->prefix}types_metafields` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `type_id` `type_id` INT UNSIGNED NOT NULL ,
            CHANGE `metafield_id` `metafield_id` INT UNSIGNED NOT NULL ,
            CHANGE `plugin_id` `plugin_id` INT UNSIGNED NULL DEFAULT NULL; 
        ALTER TABLE `{$db->prefix}users` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `entity_id` `entity_id` INT UNSIGNED NOT NULL; 
        ALTER TABLE `{$db->prefix}users_activations` 
            CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL; 
        ";
        $this->execBlock($sql);
    }
}

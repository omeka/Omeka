<?php
/**
 * Adds the started, stopped, and log fields with indices to the process table.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addPublicFeaturedIndicesToItemsAndCollectionsTables extends Omeka_Db_Migration
{
    public function up()
    {
        $db = get_db();

        // Add indices to the public and featured fields of the items table
        $sql = "ALTER TABLE `{$db->prefix}items`
                ADD INDEX `public` ( `public` ),
                ADD INDEX `featured` ( `featured` )
                ";
        $db->query($sql);
        
        // Add indices to the public and featured fields of the collections table
        $sql = "ALTER TABLE `{$db->prefix}collections`
                ADD INDEX `public` ( `public` ),
                ADD INDEX `featured` ( `featured` )
                ";
        $db->query($sql);
    }
}
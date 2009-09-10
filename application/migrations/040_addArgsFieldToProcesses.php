<?php
/**
 * Adds the `args` field to the process table.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addArgsFieldToProcesses extends Omeka_Db_Migration
{
    public function up()
    {
        // This adds the version column to the plugins table.
        $db = get_db();
        $sql = "ALTER TABLE `{$db->prefix}processes` ADD `args` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
        $db->query($sql);
    }
}
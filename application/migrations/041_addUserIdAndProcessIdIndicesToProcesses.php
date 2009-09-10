<?php
/**
 * Adds the user_id and pid indices to the process table.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addUserIdAndProcessIdIndicesToProcesses extends Omeka_Db_Migration
{
    public function up()
    {
        // This adds the version column to the plugins table.
        $db = get_db();
        $sql = "ALTER TABLE `{$db->prefix}processes` ADD INDEX `user_id` (`user_id`), ADD INDEX `pid` (`pid`)";
        $db->query($sql);
    }
}
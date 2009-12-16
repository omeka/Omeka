<?php
/**
 * Adds the started, stopped, and log fields with indices to the process table.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addStoppedStatusToProcesses extends Omeka_Db_Migration
{
    public function up()
    {
        // This adds the started, stopped, and log fields to the processes table, along with indices.
        $db = get_db();
        $sql = "ALTER TABLE `processes` CHANGE `status` `status` enum('starting','in progress','completed','paused','error','stopped') COLLATE utf8_unicode_ci NOT NULL";
        $db->query($sql);
    }
}


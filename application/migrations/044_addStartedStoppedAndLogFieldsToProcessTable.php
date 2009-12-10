<?php
/**
 * Adds the started, stopped, and log fields with indices to the process table.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addStartedStoppedAndLogFieldsToProcessTable extends Omeka_Db_Migration
{
    public function up()
    {
        // This adds the started, stopped, and log fields to the processes table, along with indices.
        $db = get_db();
        $sql = "ALTER TABLE `{$db->prefix}processes` 
                ADD `started` TIMESTAMP NOT NULL default '0000-00-00 00:00:00' AFTER `args` ,
                ADD `stopped` TIMESTAMP NOT NULL default '0000-00-00 00:00:00' AFTER `started` ,
                ADD `log` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `stopped`,
                ADD INDEX `started` ( `started` ),
                ADD INDEX `stopped` ( `stopped` )
                ";
        $db->query($sql);
    }
}


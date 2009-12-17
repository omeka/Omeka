<?php
class removeLoggingFromProcesses extends Omeka_Db_Migration
{
    public function up()
    {
        // This adds the started, stopped, and log fields to the processes table, along with indices.
        $db = get_db();
        $sql = "ALTER TABLE `{$db->prefix}processes` DROP `log` ";
        $db->query($sql);
    }
}


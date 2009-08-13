<?php
/**
 * Adds the `processes` table for background process management.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class addProcessesTable extends Omeka_Db_Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$this->db->prefix}processes` (
  `id` int unsigned NOT NULL auto_increment,
  `class` varchar(255) collate utf8_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `pid` int unsigned default NULL,
  `status` enum('starting', 'in progress', 'completed', 'paused', 'error') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL;
        $this->db->query($sql);
    }
}

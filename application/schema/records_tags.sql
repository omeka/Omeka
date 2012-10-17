CREATE TABLE IF NOT EXISTS `%PREFIX%records_tags` (
  `id` int unsigned NOT NULL auto_increment,
  `record_id` int unsigned NOT NULL,
  `record_type` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `tag_id` int unsigned NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`record_type`, `record_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

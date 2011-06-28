CREATE TABLE IF NOT EXISTS `%PREFIX%taggings` (
  `id` int unsigned NOT NULL auto_increment,
  `relation_id` int unsigned NOT NULL,
  `tag_id` int unsigned NOT NULL,
  `entity_id` int unsigned NOT NULL,
  `type` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`relation_id`,`tag_id`,`entity_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

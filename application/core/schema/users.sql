CREATE TABLE IF NOT EXISTS `%PREFIX%users` (
  `id` int unsigned NOT NULL auto_increment,
  `username` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(40) collate utf8_unicode_ci NOT NULL,
  `salt` varchar(16) collate utf8_unicode_ci default NULL,  
  `active` tinyint NOT NULL,
  `role` varchar(40) collate utf8_unicode_ci NOT NULL default 'default',
  `entity_id` int unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `active_idx` (`active`),
  KEY `entity_id` (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

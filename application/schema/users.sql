CREATE TABLE IF NOT EXISTS `%PREFIX%users` (
  `id` int unsigned NOT NULL auto_increment,
  `username` varchar(30) collate utf8_unicode_ci NOT NULL,
  `name` text collate utf8_unicode_ci NOT NULL,
  `email` text collate utf8_unicode_ci NOT NULL,
  `password` varchar(40) collate utf8_unicode_ci default NULL,
  `salt` varchar(16) collate utf8_unicode_ci default NULL,  
  `active` tinyint NOT NULL,
  `role` varchar(40) collate utf8_unicode_ci NOT NULL default 'default',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `active_idx` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `%PREFIX%plugins` (
  `id` int unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `active` tinyint NOT NULL,
  `version` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `active_idx` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

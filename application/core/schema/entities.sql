CREATE TABLE IF NOT EXISTS `%PREFIX%entities` (
  `id` int unsigned NOT NULL auto_increment,
  `first_name` text collate utf8_unicode_ci,
  `middle_name` text collate utf8_unicode_ci,
  `last_name` text collate utf8_unicode_ci,
  `email` text collate utf8_unicode_ci,
  `institution` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

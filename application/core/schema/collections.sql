CREATE TABLE IF NOT EXISTS `%PREFIX%collections` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `public` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `public` (`public`),
  KEY `featured` (`featured`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
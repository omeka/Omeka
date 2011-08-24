CREATE TABLE IF NOT EXISTS `%PREFIX%collections` (
  `id` int unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci DEFAULT NULL,
  `collectors` text collate utf8_unicode_ci DEFAULT NULL,
  `public` tinyint NOT NULL,
  `featured` tinyint NOT NULL,
  `added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `owner_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `public` (`public`),
  KEY `featured` (`featured`),
  KEY `owner_id` (`owner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

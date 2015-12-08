CREATE TABLE IF NOT EXISTS `%PREFIX%collections` (
  `id` int unsigned NOT NULL auto_increment,
  `public` tinyint NOT NULL,
  `featured` tinyint NOT NULL,
  `added` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
  `modified` timestamp NOT NULL DEFAULT '2000-01-01 00:00:00',
  `owner_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `public` (`public`),
  KEY `featured` (`featured`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

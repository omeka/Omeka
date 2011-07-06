CREATE TABLE IF NOT EXISTS `%PREFIX%entity_relationships` (
  `id` int unsigned NOT NULL auto_increment,
  `name` text collate utf8_unicode_ci,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%entity_relationships` (`id`, `name`, `description`) VALUES (1, 'added', NULL),
(2, 'modified', NULL),
(3, 'favorite', NULL),
(4, 'collector', NULL);

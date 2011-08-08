CREATE TABLE IF NOT EXISTS `%PREFIX%record_types` (
  `id` int unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%record_types` VALUES (1, 'All', 'Elements, element sets, and element texts assigned to this record type relate to all possible records.'),
(2, 'Item', 'Elements, element sets, and element texts assigned to this record type relate to item records.'),
(3, 'File', 'Elements, element sets, and element texts assigned to this record type relate to file records.');

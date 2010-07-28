CREATE TABLE IF NOT EXISTS `%PREFIX%record_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%record_types` VALUES (1, 'All', 'Elements, element sets, and element texts assigned to this record type relate to all possible records.');
INSERT INTO `%PREFIX%record_types` VALUES (2, 'Item', 'Elements, element sets, and element texts assigned to this record type relate to item records.');
INSERT INTO `%PREFIX%record_types` VALUES (3, 'File', 'Elements, element sets, and element texts assigned to this record type relate to file records.');
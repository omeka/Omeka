CREATE TABLE IF NOT EXISTS `%PREFIX%files` (
  `id` int unsigned NOT NULL auto_increment,
  `item_id` int unsigned NOT NULL,
  `order` int(10) unsigned DEFAULT NULL,
  `size` int unsigned NOT NULL,
  `has_derivative_image` tinyint(1) NOT NULL,
  `authentication` char(32) collate utf8_unicode_ci default NULL,
  `mime_type` varchar(255) collate utf8_unicode_ci default NULL,
  `type_os` varchar(255) collate utf8_unicode_ci default NULL,
  `filename` text collate utf8_unicode_ci NOT NULL,
  `original_filename` text collate utf8_unicode_ci NOT NULL,
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `added` timestamp NOT NULL default '0000-00-00 00:00:00',
  `stored` tinyint(1) NOT NULL default '0',
  `metadata` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

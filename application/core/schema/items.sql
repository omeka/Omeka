CREATE TABLE IF NOT EXISTS `%PREFIX%items` (
  `id` int unsigned NOT NULL auto_increment,
  `item_type_id` int unsigned default NULL,
  `collection_id` int unsigned default NULL,
  `featured` tinyint NOT NULL,
  `public` tinyint NOT NULL,
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `added` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `item_type_id` (`item_type_id`),
  KEY `collection_id` (`collection_id`),
  KEY `public` (`public`),
  KEY `featured` (`featured`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

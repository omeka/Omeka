CREATE TABLE IF NOT EXISTS `%PREFIX%element_texts` (
  `id` int unsigned NOT NULL auto_increment,
  `record_id` int unsigned NOT NULL,
  `record_type_id` int unsigned NOT NULL,
  `element_id` int unsigned NOT NULL,
  `html` tinyint NOT NULL,
  `text` mediumtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `record_id` (`record_id`),
  KEY `record_type_id` (`record_type_id`),
  KEY `element_id` (`element_id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

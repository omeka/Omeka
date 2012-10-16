CREATE TABLE IF NOT EXISTS `%PREFIX%element_texts` (
  `id` int unsigned NOT NULL auto_increment,
  `record_id` int unsigned NOT NULL,
  `record_type` varchar(50) NOT NULL,
  `element_id` int unsigned NOT NULL,
  `html` tinyint NOT NULL,
  `text` mediumtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `record_type_record_id` (`record_type`, `record_id`),
  KEY `element_id` (`element_id`),
  KEY `text` (`text`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

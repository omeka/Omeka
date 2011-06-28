CREATE TABLE IF NOT EXISTS `%PREFIX%entities_relations` (
  `id` int unsigned NOT NULL auto_increment,
  `entity_id` int unsigned default NULL,
  `relation_id` int unsigned default NULL,
  `relationship_id` int unsigned default NULL,
  `type` enum('Item','Collection','Exhibit') collate utf8_unicode_ci NOT NULL,
  `time` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `relation_type` (`type`),
  KEY `relation` (`relation_id`),
  KEY `relationship` (`relationship_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

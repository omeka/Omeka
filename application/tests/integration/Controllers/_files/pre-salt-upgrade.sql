CREATE TABLE `omeka_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(40) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `role` varchar(40) collate utf8_unicode_ci NOT NULL default 'default',
  `entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `active_idx` (`active`),
  KEY `entity_id` (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
UPDATE `omeka_options` SET value = "47" WHERE name = "migration" LIMIT 1;

-- This references a row in the entities table that should exist.
INSERT INTO `omeka_users` (
    `id` , `username` , `password` , `active` , `role`, `entity_id`
) VALUES (
    NULL , 'foobar', SHA1( 'foobar' ) , '1', 'admin', '1'
);
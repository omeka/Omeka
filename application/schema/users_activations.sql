CREATE TABLE IF NOT EXISTS `%PREFIX%users_activations` (
  `id` int unsigned NOT NULL auto_increment,
  `user_id` int unsigned NOT NULL,
  `url` varchar(100) collate utf8_unicode_ci default NULL,
  `added` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

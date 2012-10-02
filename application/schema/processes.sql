CREATE TABLE IF NOT EXISTS `%PREFIX%processes` (
  `id` int unsigned NOT NULL auto_increment,
  `class` varchar(255) collate utf8_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `pid` int unsigned default NULL,
  `status` enum('starting', 'in progress', 'completed', 'paused', 'error', 'stopped') collate utf8_unicode_ci NOT NULL,
  `args` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `started` timestamp NOT NULL default '0000-00-00 00:00:00',
  `stopped` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `pid` (`pid`),
  KEY `started` ( `started` ),
  KEY `stopped` ( `stopped` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
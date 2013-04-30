CREATE TABLE IF NOT EXISTS `%PREFIX%keys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `label` varchar(100) NOT NULL,
  `key` char(40) NOT NULL,
  `ip` varbinary(16) DEFAULT NULL,
  `accessed` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

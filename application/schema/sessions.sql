CREATE TABLE IF NOT EXISTS `%PREFIX%sessions` (
`id` varchar(128) collate ascii_bin,
`modified` bigint,
`lifetime` int,
`data` blob,
PRIMARY KEY (`id`),
KEY `modified` (`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

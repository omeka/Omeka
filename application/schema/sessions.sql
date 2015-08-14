CREATE TABLE IF NOT EXISTS `%PREFIX%sessions` (
`id` varchar(128),
`modified` bigint,
`lifetime` int,
`data` blob,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

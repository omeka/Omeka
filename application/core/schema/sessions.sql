CREATE TABLE IF NOT EXISTS `%PREFIX%sessions` (
`id` char(32),
`modified` bigint,
`lifetime` int,
`data` text,
PRIMARY KEY (`id`)
) ENGINE=InnoDb;

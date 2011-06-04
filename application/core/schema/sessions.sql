CREATE TABLE IF NOT EXISTS `%PREFIX%sessions` (
`id` char(32),
`modified` int,
`lifetime` int,
`data` text,
PRIMARY KEY (`id`)
) ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS `%PREFIX%data_types` (
  `id` int unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%data_types` VALUES (1, 'Text', 'A long, typically multi-line text string. Up to 65535 characters.'),
(2, 'Tiny Text', 'A short, typically one-line text string. Up to 255 characters.'),
(3, 'Date Range', 'A date range, begin to end. In format yyyy-mm-dd yyyy-mm-dd.'),
(4, 'Integer', 'Set of numbers consisting of the natural numbers including 0 (0, 1, 2, 3, ...) and their negatives (0, âˆ’1, âˆ’2, âˆ’3, ...).'),
(9, 'Date', 'A date in format yyyy-mm-dd'),
(10, 'Date Time', 'A date and time combination in the format: yyyy-mm-dd hh:mm:ss');

CREATE TABLE IF NOT EXISTS `%PREFIX%element_sets` (
  `id` int unsigned NOT NULL auto_increment,
  `record_type_id` int unsigned NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `record_type_id` (`record_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%element_sets` VALUES 
(1, 1, 'Dublin Core', 'The Dublin Core metadata element set. These elements are common to all Omeka resources, including items, files, collections, exhibits, and entities. See http://dublincore.org/documents/dces/.'),
(3, 2, 'Item Type Metadata', 'The item type metadata element set, consisting of all item type elements bundled with Omeka and all item type elements created by an administrator.'),
(4, 3, 'Omeka Legacy File', 'The metadata element set that, in addition to the Dublin Core element set, was included in the `files` table in previous versions of Omeka. These elements are common to all Omeka files. This set may be deprecated in future versions.'),
(5, 3, 'Omeka Image File', 'The metadata element set that was included in the `files_images` table in previous versions of Omeka. These elements are common to all image files.'),
(6, 3, 'Omeka Video File', 'The metadata element set that was included in the `files_videos` table in previous versions of Omeka. These elements are common to all video files.');

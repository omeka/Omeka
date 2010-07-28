CREATE TABLE IF NOT EXISTS `%PREFIX%item_types_elements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_type_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `order` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `item_type_id_element_id` (`item_type_id`,`element_id`),
  KEY `item_type_id` (`item_type_id`),
  KEY `element_id` (`element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%item_types_elements` VALUES (1, 1, 7, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (2, 1, 1, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (3, 6, 7, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (6, 6, 10, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (7, 3, 7, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (8, 3, 11, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (9, 3, 12, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (10, 3, 13, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (11, 3, 14, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (12, 3, 5, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (13, 5, 7, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (14, 5, 11, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (15, 5, 15, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (16, 5, 5, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (17, 4, 7, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (18, 4, 11, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (19, 4, 15, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (20, 4, 5, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (21, 4, 2, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (22, 4, 3, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (23, 4, 4, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (24, 4, 16, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (25, 9, 17, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (26, 9, 18, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (27, 9, 20, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (28, 9, 19, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (29, 9, 21, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (30, 9, 22, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (31, 9, 23, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (32, 10, 24, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (33, 10, 25, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (34, 10, 26, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (35, 10, 11, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (36, 10, 27, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (37, 7, 6, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (38, 11, 28, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (39, 8, 29, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (40, 8, 30, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (41, 8, 11, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (42, 12, 31, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (43, 12, 32, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (44, 12, 33, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (45, 12, 34, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (46, 12, 35, null);
INSERT INTO `%PREFIX%item_types_elements` VALUES (47, 12, 36, null);
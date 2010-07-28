CREATE TABLE IF NOT EXISTS `%PREFIX%mime_element_set_lookup` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `element_set_id` int(10) unsigned NOT NULL,
  `mime` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `mime` (`mime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (1, 5, 'image/bmp');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (2, 5, 'image/gif');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (3, 5, 'image/ief');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (4, 5, 'image/jpeg');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (5, 5, 'image/pict');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (6, 5, 'image/pjpeg');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (7, 5, 'image/png');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (8, 5, 'image/tiff');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (9, 5, 'image/vnd.rn-realflash');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (10, 5, 'image/vnd.rn-realpix');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (11, 5, 'image/vnd.wap.wbmp');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (12, 5, 'image/x-icon');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (13, 5, 'image/x-jg');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (14, 5, 'image/x-jps');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (15, 5, 'image/x-niff');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (16, 5, 'image/x-pcx');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (17, 5, 'image/x-pict');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (18, 5, 'image/x-quicktime');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (19, 5, 'image/x-rgb');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (20, 5, 'image/x-tiff');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (21, 5, 'image/x-windows-bmp');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (22, 5, 'image/x-xbitmap');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (23, 5, 'image/x-xbm');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (24, 5, 'image/x-xpixmap');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (25, 5, 'image/x-xwd');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (26, 5, 'image/x-xwindowdump');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (27, 6, 'video/x-msvideo');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (28, 6, 'video/avi');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (29, 6, 'video/msvideo');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (30, 6, 'video/x-mpeg');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (31, 6, 'video/x-ms-asf');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (32, 6, 'video/mpeg');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (33, 6, 'video/quicktime');
INSERT INTO `%PREFIX%mime_element_set_lookup` VALUES (34, 6, 'video/x-ms-wmv');
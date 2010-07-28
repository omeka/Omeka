CREATE TABLE IF NOT EXISTS `%PREFIX%item_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `%PREFIX%item_types` VALUES (1, 'Document', 'A resource containing textual data.  Note that facsimiles or images of texts are still of the genre text.');
INSERT INTO `%PREFIX%item_types` VALUES (3, 'Moving Image', 'A series of visual representations that, when shown in succession, impart an impression of motion.');
INSERT INTO `%PREFIX%item_types` VALUES (4, 'Oral History', 'A resource containing historical information obtained in interviews with persons having firsthand knowledge.');
INSERT INTO `%PREFIX%item_types` VALUES (5, 'Sound', 'A resource whose content is primarily intended to be rendered as audio.');
INSERT INTO `%PREFIX%item_types` VALUES (6, 'Still Image', 'A static visual representation. Examples of still images are: paintings, drawings, graphic designs, plans and maps.  Recommended best practice is to assign the type "text" to images of textual materials.');
INSERT INTO `%PREFIX%item_types` VALUES (7, 'Website', 'A resource comprising of a web page or web pages and all related assets ( such as images, sound and video files, etc. ).');
INSERT INTO `%PREFIX%item_types` VALUES (8, 'Event', 'A non-persistent, time-based occurrence.  Metadata for an event provides descriptive information that is the basis for discovery of the purpose, location, duration, and responsible agents associated with an event. Examples include an exhibition, webcast, conference, workshop, open day, performance, battle, trial, wedding, tea party, conflagration.');
INSERT INTO `%PREFIX%item_types` VALUES (9, 'Email', 'A resource containing textual messages and binary attachments sent electronically from one person to another or one person to many people.');
INSERT INTO `%PREFIX%item_types` VALUES (10, 'Lesson Plan', 'Instructional materials.');
INSERT INTO `%PREFIX%item_types` VALUES (11, 'Hyperlink', 'Title, URL, Description or annotation.');
INSERT INTO `%PREFIX%item_types` VALUES (12, 'Person', 'An individual, biographical data, birth and death, etc.');
INSERT INTO `%PREFIX%item_types` VALUES (13, 'Interactive Resource', 'A resource requiring interaction from the user to be understood, executed, or experienced. Examples include forms on Web pages, applets, multimedia learning objects, chat services, or virtual reality environments.');
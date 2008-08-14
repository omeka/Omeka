<?php 
$install_sql = <<<SQL
CREATE TABLE IF NOT EXISTS `{$db->prefix}collections` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `public` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `data_types`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}data_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `data_types`
-- 

INSERT INTO `{$db->prefix}data_types` VALUES (1, 'Text', 'A long, typically multi-line text string. Up to 65535 characters.');
INSERT INTO `{$db->prefix}data_types` VALUES (2, 'Tiny Text', 'A short, typically one-line text string. Up to 255 characters.');
INSERT INTO `{$db->prefix}data_types` VALUES (3, 'Date Range', 'A date range, begin to end. In format yyyy-mm-dd yyyy-mm-dd.');
INSERT INTO `{$db->prefix}data_types` VALUES (4, 'Integer', 'Set of numbers consisting of the natural numbers including 0 (0, 1, 2, 3, ...) and their negatives (0, âˆ’1, âˆ’2, âˆ’3, ...).');
INSERT INTO `{$db->prefix}data_types` VALUES (5, 'Floating Point', 'A number with a specified number decimal places, or fractional part.');
INSERT INTO `{$db->prefix}data_types` VALUES (6, 'Boolean', 'A primitive datatype having one of two values: true or false, off or on, 1 or 0.');
INSERT INTO `{$db->prefix}data_types` VALUES (7, 'Time', 'A time in format hh:mm:ss');
INSERT INTO `{$db->prefix}data_types` VALUES (8, 'Time Range', 'A time range in format hh:mm:ss hh:mm:ss');
INSERT INTO `{$db->prefix}data_types` VALUES (9, 'Date', 'A date in format yyyy-mm-dd');
INSERT INTO `{$db->prefix}data_types` VALUES (10, 'Date Time', 'A date and time combination in the format: yyyy-mm-dd hh:mm:ss');
INSERT INTO `{$db->prefix}data_types` VALUES (11, 'Date Time Range', 'A date and time combination range, begin to end. In format yyyy-mm-dd hh:mm:ss yyyy-mm-dd hh:mm:ss');

-- --------------------------------------------------------

-- 
-- Table structure for table `elements`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}elements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `record_type_id` int(10) unsigned NOT NULL,
  `data_type_id` int(10) unsigned NOT NULL,
  `element_set_id` int(10) unsigned NOT NULL,
  `order` int(10) unsigned default NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_element_set_id` (`element_set_id`,`name`),
  UNIQUE KEY `order_element_set_id` (`element_set_id`,`order`),
  KEY `record_type_id` (`record_type_id`),
  KEY `data_type_id` (`data_type_id`),
  KEY `element_set_id` (`element_set_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Dumping data for table `elements`
-- 

INSERT INTO `{$db->prefix}elements` VALUES (1, 2, 1, 3, NULL, 'Text', 'Any textual data included in the document.');
INSERT INTO `{$db->prefix}elements` VALUES (2, 2, 1, 3, NULL, 'Interviewer', 'The person(s) performing the interview.');
INSERT INTO `{$db->prefix}elements` VALUES (3, 2, 1, 3, NULL, 'Interviewee', 'The person(s) being interviewed.');
INSERT INTO `{$db->prefix}elements` VALUES (4, 2, 1, 3, NULL, 'Location', 'The location of the interview.');
INSERT INTO `{$db->prefix}elements` VALUES (5, 2, 1, 3, NULL, 'Transcription', 'Any written text transcribed from a sound.');
INSERT INTO `{$db->prefix}elements` VALUES (6, 2, 1, 3, NULL, 'Local URL', 'The URL of the local directory containing all assets of the website.');
INSERT INTO `{$db->prefix}elements` VALUES (7, 2, 1, 3, NULL, 'Original Format', 'If the image is of an object, state the type of object, such as painting, sculpture, paper, photo, and additional data');
INSERT INTO `{$db->prefix}elements` VALUES (10, 2, 1, 3, NULL, 'Physical Dimensions', 'The actual physical size of the original image.');
INSERT INTO `{$db->prefix}elements` VALUES (11, 2, 1, 3, NULL, 'Duration', 'Length of time involved (seconds, minutes, hours, days, class periods, etc.)');
INSERT INTO `{$db->prefix}elements` VALUES (12, 2, 1, 3, NULL, 'Compression', 'Type/rate of compression for moving image file (i.e. MPEG-4)');
INSERT INTO `{$db->prefix}elements` VALUES (13, 2, 1, 3, NULL, 'Producer', 'Name (or names) of the person who produced the video.');
INSERT INTO `{$db->prefix}elements` VALUES (14, 2, 1, 3, NULL, 'Director', 'Name (or names) of the person who produced the video.');
INSERT INTO `{$db->prefix}elements` VALUES (15, 2, 1, 3, NULL, 'Bit Rate/Frequency', 'Rate at which bits are transferred (i.e. 96 kbit/s would be FM quality audio)');
INSERT INTO `{$db->prefix}elements` VALUES (16, 2, 1, 3, NULL, 'Time Summary', 'A summary of an interview given for different time stamps throughout the interview');
INSERT INTO `{$db->prefix}elements` VALUES (17, 2, 1, 3, NULL, 'Email Body', 'The main body of the email, including all replied and forwarded text and headers.');
INSERT INTO `{$db->prefix}elements` VALUES (18, 2, 1, 3, NULL, 'Subject Line', 'The content of the subject line of the email.');
INSERT INTO `{$db->prefix}elements` VALUES (19, 2, 1, 3, NULL, 'From', 'The name and email address of the person sending the email.');
INSERT INTO `{$db->prefix}elements` VALUES (20, 2, 1, 3, NULL, 'To', 'The name(s) and email address(es) of the person to whom the email was sent.');
INSERT INTO `{$db->prefix}elements` VALUES (21, 2, 1, 3, NULL, 'CC', 'The name(s) and email address(es) of the person to whom the email was carbon copied.');
INSERT INTO `{$db->prefix}elements` VALUES (22, 2, 1, 3, NULL, 'BCC', 'The name(s) and email address(es) of the person to whom the email was blind carbon copied.');
INSERT INTO `{$db->prefix}elements` VALUES (23, 2, 1, 3, NULL, 'Number of Attachments', 'The number of attachments to the email.');
INSERT INTO `{$db->prefix}elements` VALUES (24, 2, 1, 3, NULL, 'Standards', '');
INSERT INTO `{$db->prefix}elements` VALUES (25, 2, 1, 3, NULL, 'Objectives', '');
INSERT INTO `{$db->prefix}elements` VALUES (26, 2, 1, 3, NULL, 'Materials', '');
INSERT INTO `{$db->prefix}elements` VALUES (27, 2, 1, 3, NULL, 'Lesson Plan Text', '');
INSERT INTO `{$db->prefix}elements` VALUES (28, 2, 1, 3, NULL, 'URL', '');
INSERT INTO `{$db->prefix}elements` VALUES (29, 2, 1, 3, NULL, 'Event Type', '');
INSERT INTO `{$db->prefix}elements` VALUES (30, 2, 1, 3, NULL, 'Participants', 'Names of individuals or groups participating in the event.');
INSERT INTO `{$db->prefix}elements` VALUES (31, 2, 1, 3, NULL, 'Birth Date', '');
INSERT INTO `{$db->prefix}elements` VALUES (32, 2, 1, 3, NULL, 'Birthplace', '');
INSERT INTO `{$db->prefix}elements` VALUES (33, 2, 1, 3, NULL, 'Death Date', '');
INSERT INTO `{$db->prefix}elements` VALUES (34, 2, 1, 3, NULL, 'Occupation', '');
INSERT INTO `{$db->prefix}elements` VALUES (35, 2, 1, 3, NULL, 'Biographical Text', '');
INSERT INTO `{$db->prefix}elements` VALUES (36, 2, 1, 3, NULL, 'Bibliography', '');
INSERT INTO `{$db->prefix}elements` VALUES (37, 1, 1, 1, 8, 'Contributor', 'An entity responsible for making contributions to the resource. Examples of a Contributor include a person, an organization, or a service. Typically, the name of a Contributor should be used to indicate the entity.');
INSERT INTO `{$db->prefix}elements` VALUES (38, 1, 1, 1, 15, 'Coverage', 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant. Spatial topic and spatial applicability may be a named place or a location specified by its geographic coordinates. Temporal topic may be a named period, date, or date range. A jurisdiction may be a named administrative entity or a geographic place to which the resource applies. Recommended best practice is to use a controlled vocabulary such as the Thesaurus of Geographic Names [TGN]. Where appropriate, named places or time periods can be used in preference to numeric identifiers such as sets of coordinates or date ranges.');
INSERT INTO `{$db->prefix}elements` VALUES (39, 1, 1, 1, 4, 'Creator', 'An entity primarily responsible for making the resource. Examples of a Creator include a person, an organization, or a service. Typically, the name of a Creator should be used to indicate the entity.');
INSERT INTO `{$db->prefix}elements` VALUES (40, 1, 1, 1, 7, 'Date', 'A point or period of time associated with an event in the lifecycle of the resource. Date may be used to express temporal information at any level of granularity. Recommended best practice is to use an encoding scheme, such as the W3CDTF profile of ISO 8601 [W3CDTF].');
INSERT INTO `{$db->prefix}elements` VALUES (41, 1, 1, 1, 3, 'Description', 'An account of the resource. Description may include but is not limited to: an abstract, a table of contents, a graphical representation, or a free-text account of the resource.');
INSERT INTO `{$db->prefix}elements` VALUES (42, 1, 1, 1, 11, 'Format', 'The file format, physical medium, or dimensions of the resource. Examples of dimensions include size and duration. Recommended best practice is to use a controlled vocabulary such as the list of Internet Media Types [MIME].');
INSERT INTO `{$db->prefix}elements` VALUES (43, 1, 1, 1, 14, 'Identifier', 'An unambiguous reference to the resource within a given context. Recommended best practice is to identify the resource by means of a string conforming to a formal identification system.');
INSERT INTO `{$db->prefix}elements` VALUES (44, 1, 1, 1, 12, 'Language', 'A language of the resource. Recommended best practice is to use a controlled vocabulary such as RFC 4646 [RFC4646].');
INSERT INTO `{$db->prefix}elements` VALUES (45, 1, 1, 1, 6, 'Publisher', 'An entity responsible for making the resource available. Examples of a Publisher include a person, an organization, or a service. Typically, the name of a Publisher should be used to indicate the entity.');
INSERT INTO `{$db->prefix}elements` VALUES (46, 1, 1, 1, 10, 'Relation', 'A related resource. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.');
INSERT INTO `{$db->prefix}elements` VALUES (47, 1, 1, 1, 9, 'Rights', 'Information about rights held in and over the resource. Typically, rights information includes a statement about various property rights associated with the resource, including intellectual property rights.');
INSERT INTO `{$db->prefix}elements` VALUES (48, 1, 1, 1, 5, 'Source', 'A related resource from which the described resource is derived. The described resource may be derived from the related resource in whole or in part. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.');
INSERT INTO `{$db->prefix}elements` VALUES (49, 1, 1, 1, 2, 'Subject', 'The topic of the resource. Typically, the subject will be represented using keywords, key phrases, or classification codes. Recommended best practice is to use a controlled vocabulary. To describe the spatial or temporal topic of the resource, use the Coverage element.');
INSERT INTO `{$db->prefix}elements` VALUES (50, 1, 1, 1, 1, 'Title', 'A name given to the resource. Typically, a Title will be a name by which the resource is formally known.');
INSERT INTO `{$db->prefix}elements` VALUES (51, 1, 1, 1, 13, 'Type', 'The nature or genre of the resource. Recommended best practice is to use a controlled vocabulary such as the DCMI Type Vocabulary [DCMITYPE]. To describe the file format, physical medium, or dimensions of the resource, use the Format element.');
INSERT INTO `{$db->prefix}elements` VALUES (52, 2, 1, 2, 3, 'Spatial Coverage', '');
INSERT INTO `{$db->prefix}elements` VALUES (53, 2, 1, 2, 1, 'Additional Creator', '');
INSERT INTO `{$db->prefix}elements` VALUES (54, 2, 1, 2, 2, 'Rights Holder', '');
INSERT INTO `{$db->prefix}elements` VALUES (55, 2, 1, 2, 5, 'Provenance', '');
INSERT INTO `{$db->prefix}elements` VALUES (56, 2, 1, 2, 6, 'Citation', '');
INSERT INTO `{$db->prefix}elements` VALUES (57, 2, 3, 2, 4, 'Temporal Coverage', '');
INSERT INTO `{$db->prefix}elements` VALUES (58, 3, 1, 4, 1, 'Additional Creator', '');
INSERT INTO `{$db->prefix}elements` VALUES (59, 3, 1, 4, 2, 'Transcriber', '');
INSERT INTO `{$db->prefix}elements` VALUES (60, 3, 1, 4, 3, 'Producer', '');
INSERT INTO `{$db->prefix}elements` VALUES (61, 3, 1, 4, 4, 'Render Device', '');
INSERT INTO `{$db->prefix}elements` VALUES (62, 3, 1, 4, 5, 'Render Details', '');
INSERT INTO `{$db->prefix}elements` VALUES (63, 3, 10, 4, 6, 'Capture Date', '');
INSERT INTO `{$db->prefix}elements` VALUES (64, 3, 1, 4, 7, 'Capture Device', '');
INSERT INTO `{$db->prefix}elements` VALUES (65, 3, 1, 4, 8, 'Capture Details', '');
INSERT INTO `{$db->prefix}elements` VALUES (66, 3, 1, 4, 9, 'Change History', '');
INSERT INTO `{$db->prefix}elements` VALUES (67, 3, 1, 4, 10, 'Watermark', '');
INSERT INTO `{$db->prefix}elements` VALUES (69, 3, 1, 4, 12, 'Encryption', '');
INSERT INTO `{$db->prefix}elements` VALUES (70, 3, 1, 4, 13, 'Compression', '');
INSERT INTO `{$db->prefix}elements` VALUES (71, 3, 1, 4, 14, 'Post Processing', '');
INSERT INTO `{$db->prefix}elements` VALUES (72, 3, 4, 5, 1, 'Width', '');
INSERT INTO `{$db->prefix}elements` VALUES (73, 3, 4, 5, 2, 'Height', '');
INSERT INTO `{$db->prefix}elements` VALUES (74, 3, 4, 5, 3, 'Bit Depth', '');
INSERT INTO `{$db->prefix}elements` VALUES (75, 3, 4, 5, 4, 'Channels', '');
INSERT INTO `{$db->prefix}elements` VALUES (76, 3, 1, 5, 5, 'Exif String', '');
INSERT INTO `{$db->prefix}elements` VALUES (77, 3, 1, 5, 6, 'Exif Array', '');
INSERT INTO `{$db->prefix}elements` VALUES (78, 3, 1, 5, 7, 'IPTC String', '');
INSERT INTO `{$db->prefix}elements` VALUES (79, 3, 1, 5, 8, 'IPTC Array', '');
INSERT INTO `{$db->prefix}elements` VALUES (80, 3, 4, 6, 1, 'Bitrate', '');
INSERT INTO `{$db->prefix}elements` VALUES (81, 3, 4, 6, 2, 'Duration', '');
INSERT INTO `{$db->prefix}elements` VALUES (82, 3, 4, 6, 3, 'Sample Rate', '');
INSERT INTO `{$db->prefix}elements` VALUES (83, 3, 1, 6, 4, 'Codec', '');
INSERT INTO `{$db->prefix}elements` VALUES (84, 3, 4, 6, 5, 'Width', '');
INSERT INTO `{$db->prefix}elements` VALUES (85, 3, 4, 6, 6, 'Height', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `element_sets`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}element_sets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `record_type_id` int(10) unsigned NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `record_type_id` (`record_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `element_sets`
-- 

INSERT INTO `{$db->prefix}element_sets` VALUES (1, 1, 'Dublin Core', 'The Dublin Core metadata element set. These elements are common to all Omeka resourses, including items, files, collections, exhibits, and entities. See http://dublincore.org/documents/dces/.');
INSERT INTO `{$db->prefix}element_sets` VALUES (2, 2, 'Omeka Legacy Item', 'The metadata element set that, in addition to the Dublin Core element set, was included in the `items` table in previous versions of Omeka. These elements are common to all Omeka items. This set may be deprecated in future versions.');
INSERT INTO `{$db->prefix}element_sets` VALUES (3, 2, 'Item Type', 'The item type metadata element set, consisting of all item type elements bundled with Omeka and all item type elements created by an administrator.');
INSERT INTO `{$db->prefix}element_sets` VALUES (4, 3, 'Omeka Legacy File', 'The metadata element set that, in addition to the Dublin Core element set, was included in the `files` table in previous versions of Omeka. These elements are common to all Omeka files. This set may be deprecated in future versions.');
INSERT INTO `{$db->prefix}element_sets` VALUES (5, 3, 'Omeka Image File', 'The metadata element set that was included in the `files_images` table in previous versions of Omeka. These elements are common to all image files.');
INSERT INTO `{$db->prefix}element_sets` VALUES (6, 3, 'Omeka Video File', 'The metadata element set that was included in the `files_videos` table in previous versions of Omeka. These elements are common to all video files.');
INSERT INTO `{$db->prefix}element_sets` VALUES (7, 2, 'Generic Item', 'The generic item metadata element set, consisting of all item elements created by an administrator and not assigned to an item type, and elements created by plugins and not assigned to an item type or element set.');

-- --------------------------------------------------------

-- 
-- Table structure for table `element_texts`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}element_texts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `record_id` int(10) unsigned NOT NULL,
  `record_type_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `html` tinyint(1) NOT NULL,
  `text` mediumtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `record_id` (`record_id`),
  KEY `record_type_id` (`record_type_id`),
  KEY `element_id` (`element_id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `entities`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}entities` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `first_name` text collate utf8_unicode_ci,
  `middle_name` text collate utf8_unicode_ci,
  `last_name` text collate utf8_unicode_ci,
  `email` text collate utf8_unicode_ci,
  `institution` text collate utf8_unicode_ci,
  `parent_id` int(10) unsigned default NULL,
  `type` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

-- 
-- Table structure for table `entities_relations`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}entities_relations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `entity_id` int(10) unsigned default NULL,
  `relation_id` int(10) unsigned default NULL,
  `relationship_id` int(10) unsigned default NULL,
  `type` enum('Item','Collection','Exhibit') collate utf8_unicode_ci NOT NULL,
  `time` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `relation_type` (`type`),
  KEY `relation` (`relation_id`),
  KEY `relationship` (`relationship_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `entity_relationships`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}entity_relationships` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` text collate utf8_unicode_ci,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `{$db->prefix}entity_relationships` (`id`, `name`, `description`) VALUES (1, 'added', NULL),
(2, 'modified', NULL),
(3, 'favorite', NULL),
(4, 'collector', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `exhibits`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}exhibits` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci default NULL,
  `description` text collate utf8_unicode_ci,
  `credits` text collate utf8_unicode_ci,
  `featured` tinyint(1) default '0',
  `public` tinyint(1) default '0',
  `theme` varchar(30) collate utf8_unicode_ci default NULL,
  `slug` varchar(30) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `public` (`public`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}files` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `has_derivative_image` tinyint(1) NOT NULL,
  `authentication` char(32) collate utf8_unicode_ci default NULL,
  `mime_browser` varchar(255) collate utf8_unicode_ci default NULL,
  `mime_os` varchar(255) collate utf8_unicode_ci default NULL,
  `type_os` varchar(255) collate utf8_unicode_ci default NULL,
  `archive_filename` text collate utf8_unicode_ci NOT NULL,
  `original_filename` text collate utf8_unicode_ci NOT NULL,
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `added` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `items`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_type_id` int(10) unsigned default NULL,
  `collection_id` int(10) unsigned default NULL,
  `featured` tinyint(1) NOT NULL,
  `public` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `item_type_id` (`item_type_id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `items_section_pages`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}items_section_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned default NULL,
  `page_id` int(10) unsigned NOT NULL,
  `text` text collate utf8_unicode_ci,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `item_types`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}item_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `item_types`
-- 

INSERT INTO `{$db->prefix}item_types` VALUES (1, 'Document', 'A resource containing textual data.  Note that facsimiles or images of texts are still of the genre text.');
INSERT INTO `{$db->prefix}item_types` VALUES (3, 'Moving Image', 'A series of visual representations that, when shown in succession, impart an impression of motion.');
INSERT INTO `{$db->prefix}item_types` VALUES (4, 'Oral History', 'A resource containing historical information obtained in interviews with persons having firsthand knowledge.');
INSERT INTO `{$db->prefix}item_types` VALUES (5, 'Sound', 'A resource whose content is primarily intended to be rendered as audio.');
INSERT INTO `{$db->prefix}item_types` VALUES (6, 'Still Image', 'A static visual representation. Examples of still images are: paintings, drawings, graphic designs, plans and maps.  Recommended best practice is to assign the type "text" to images of textual materials.');
INSERT INTO `{$db->prefix}item_types` VALUES (7, 'Website', 'A resource comprising of a web page or web pages and all related assets ( such as images, sound and video files, etc. ).');
INSERT INTO `{$db->prefix}item_types` VALUES (8, 'Event', 'A non-persistent, time-based occurrence.  Metadata for an event provides descriptive information that is the basis for discovery of the purpose, location, duration, and responsible agents associated with an event. Examples include an exhibition, webcast, conference, workshop, open day, performance, battle, trial, wedding, tea party, conflagration.');
INSERT INTO `{$db->prefix}item_types` VALUES (9, 'Email', 'A resource containing textual messages and binary attachments sent electronically from one person to another or one person to many people.');
INSERT INTO `{$db->prefix}item_types` VALUES (10, 'Lesson Plan', 'Instructional materials.');
INSERT INTO `{$db->prefix}item_types` VALUES (11, 'Hyperlink', 'Title, URL, Description or annotation.');
INSERT INTO `{$db->prefix}item_types` VALUES (12, 'Person', 'An individual, biographical data, birth and death, etc.');
INSERT INTO `{$db->prefix}item_types` VALUES (13, 'Interactive Resource', 'A resource requiring interaction from the user to be understood, executed, or experienced. Examples include forms on Web pages, applets, multimedia learning objects, chat services, or virtual reality environments.');

-- --------------------------------------------------------

-- 
-- Table structure for table `item_types_elements`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}item_types_elements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item_type_id` int(10) unsigned NOT NULL,
  `element_id` int(10) unsigned NOT NULL,
  `order` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `item_type_id_element_id` (`item_type_id`,`element_id`),
  KEY `item_type_id` (`item_type_id`),
  KEY `element_id` (`element_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `item_types_elements`
-- 

INSERT INTO `{$db->prefix}item_types_elements` VALUES (1, 1, 7, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (2, 1, 1, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (3, 6, 7, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (6, 6, 10, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (7, 3, 7, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (8, 3, 11, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (9, 3, 12, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (10, 3, 13, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (11, 3, 14, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (12, 3, 5, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (13, 5, 7, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (14, 5, 11, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (15, 5, 15, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (16, 5, 5, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (17, 4, 7, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (18, 4, 11, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (19, 4, 15, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (20, 4, 5, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (21, 4, 2, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (22, 4, 3, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (23, 4, 4, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (24, 4, 16, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (25, 9, 17, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (26, 9, 18, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (27, 9, 20, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (28, 9, 19, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (29, 9, 21, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (30, 9, 22, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (31, 9, 23, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (32, 10, 24, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (33, 10, 25, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (34, 10, 26, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (35, 10, 11, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (36, 10, 27, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (37, 7, 6, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (38, 11, 28, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (39, 8, 29, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (40, 8, 30, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (41, 8, 11, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (42, 12, 31, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (43, 12, 32, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (44, 12, 33, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (45, 12, 34, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (46, 12, 35, null);
INSERT INTO `{$db->prefix}item_types_elements` VALUES (47, 12, 36, null);

-- --------------------------------------------------------

-- 
-- Table structure for table `mime_element_set_lookup`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}mime_element_set_lookup` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `element_set_id` int(10) unsigned NOT NULL,
  `mime` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `mime` (`mime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `mime_element_set_lookup`
-- 

INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (1, 5, 'image/bmp');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (2, 5, 'image/gif');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (3, 5, 'image/ief');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (4, 5, 'image/jpeg');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (5, 5, 'image/pict');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (6, 5, 'image/pjpeg');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (7, 5, 'image/png');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (8, 5, 'image/tiff');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (9, 5, 'image/vnd.rn-realflash');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (10, 5, 'image/vnd.rn-realpix');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (11, 5, 'image/vnd.wap.wbmp');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (12, 5, 'image/x-icon');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (13, 5, 'image/x-jg');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (14, 5, 'image/x-jps');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (15, 5, 'image/x-niff');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (16, 5, 'image/x-pcx');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (17, 5, 'image/x-pict');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (18, 5, 'image/x-quicktime');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (19, 5, 'image/x-rgb');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (20, 5, 'image/x-tiff');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (21, 5, 'image/x-windows-bmp');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (22, 5, 'image/x-xbitmap');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (23, 5, 'image/x-xbm');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (24, 5, 'image/x-xpixmap');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (25, 5, 'image/x-xwd');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (26, 5, 'image/x-xwindowdump');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (27, 6, 'video/x-msvideo');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (28, 6, 'video/avi');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (29, 6, 'video/msvideo');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (30, 6, 'video/x-mpeg');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (31, 6, 'video/x-ms-asf');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (32, 6, 'video/mpeg');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (33, 6, 'video/quicktime');
INSERT INTO `{$db->prefix}mime_element_set_lookup` VALUES (34, 6, 'video/x-ms-wmv');

-- --------------------------------------------------------

-- 
-- Table structure for table `options`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}options` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(200) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `plugins`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}plugins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `active_idx` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `record_types`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}record_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `record_types`
-- 

INSERT INTO `{$db->prefix}record_types` VALUES (1, 'All', 'Elements, element sets, and element texts assigned to this record type relate to all possible records.');
INSERT INTO `{$db->prefix}record_types` VALUES (2, 'Item', 'Elements, element sets, and element texts assigned to this record type relate to item records.');
INSERT INTO `{$db->prefix}record_types` VALUES (3, 'File', 'Elements, element sets, and element texts assigned to this record type relate to file records.');

-- --------------------------------------------------------

-- 
-- Table structure for table `section_pages`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}section_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `section_id` int(10) unsigned NOT NULL,
  `layout` varchar(255) collate utf8_unicode_ci default NULL,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `sections`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}sections` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci default NULL,
  `description` text collate utf8_unicode_ci,
  `exhibit_id` int(10) unsigned NOT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  `slug` varchar(30) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `tags`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `taggings`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}taggings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `relation_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `type` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`relation_id`,`tag_id`,`entity_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(40) collate utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  `role` varchar(40) collate utf8_unicode_ci NOT NULL default 'default',
  `entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `active_idx` (`active`),
  KEY `entity_id` (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `users_activations`
-- 

CREATE TABLE IF NOT EXISTS `{$db->prefix}users_activations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `url` varchar(100) collate utf8_unicode_ci default NULL,
  `added` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SQL;

?>

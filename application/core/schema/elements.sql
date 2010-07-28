CREATE TABLE IF NOT EXISTS `%PREFIX%elements` (
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

INSERT INTO `%PREFIX%elements` VALUES (1, 2, 1, 3, NULL, 'Text', 'Any textual data included in the document.');
INSERT INTO `%PREFIX%elements` VALUES (2, 2, 2, 3, NULL, 'Interviewer', 'The person(s) performing the interview.');
INSERT INTO `%PREFIX%elements` VALUES (3, 2, 2, 3, NULL, 'Interviewee', 'The person(s) being interviewed.');
INSERT INTO `%PREFIX%elements` VALUES (4, 2, 2, 3, NULL, 'Location', 'The location of the interview.');
INSERT INTO `%PREFIX%elements` VALUES (5, 2, 1, 3, NULL, 'Transcription', 'Any written text transcribed from a sound.');
INSERT INTO `%PREFIX%elements` VALUES (6, 2, 2, 3, NULL, 'Local URL', 'The URL of the local directory containing all assets of the website.');
INSERT INTO `%PREFIX%elements` VALUES (7, 2, 2, 3, NULL, 'Original Format', 'If the image is of an object, state the type of object, such as painting, sculpture, paper, photo, and additional data');
INSERT INTO `%PREFIX%elements` VALUES (10, 2, 2, 3, NULL, 'Physical Dimensions', 'The actual physical size of the original image.');
INSERT INTO `%PREFIX%elements` VALUES (11, 2, 2, 3, NULL, 'Duration', 'Length of time involved (seconds, minutes, hours, days, class periods, etc.)');
INSERT INTO `%PREFIX%elements` VALUES (12, 2, 2, 3, NULL, 'Compression', 'Type/rate of compression for moving image file (i.e. MPEG-4)');
INSERT INTO `%PREFIX%elements` VALUES (13, 2, 2, 3, NULL, 'Producer', 'Name (or names) of the person who produced the video.');
INSERT INTO `%PREFIX%elements` VALUES (14, 2, 2, 3, NULL, 'Director', 'Name (or names) of the person who produced the video.');
INSERT INTO `%PREFIX%elements` VALUES (15, 2, 2, 3, NULL, 'Bit Rate/Frequency', 'Rate at which bits are transferred (i.e. 96 kbit/s would be FM quality audio)');
INSERT INTO `%PREFIX%elements` VALUES (16, 2, 2, 3, NULL, 'Time Summary', 'A summary of an interview given for different time stamps throughout the interview');
INSERT INTO `%PREFIX%elements` VALUES (17, 2, 1, 3, NULL, 'Email Body', 'The main body of the email, including all replied and forwarded text and headers.');
INSERT INTO `%PREFIX%elements` VALUES (18, 2, 2, 3, NULL, 'Subject Line', 'The content of the subject line of the email.');
INSERT INTO `%PREFIX%elements` VALUES (19, 2, 2, 3, NULL, 'From', 'The name and email address of the person sending the email.');
INSERT INTO `%PREFIX%elements` VALUES (20, 2, 2, 3, NULL, 'To', 'The name(s) and email address(es) of the person to whom the email was sent.');
INSERT INTO `%PREFIX%elements` VALUES (21, 2, 2, 3, NULL, 'CC', 'The name(s) and email address(es) of the person to whom the email was carbon copied.');
INSERT INTO `%PREFIX%elements` VALUES (22, 2, 2, 3, NULL, 'BCC', 'The name(s) and email address(es) of the person to whom the email was blind carbon copied.');
INSERT INTO `%PREFIX%elements` VALUES (23, 2, 2, 3, NULL, 'Number of Attachments', 'The number of attachments to the email.');
INSERT INTO `%PREFIX%elements` VALUES (24, 2, 1, 3, NULL, 'Standards', '');
INSERT INTO `%PREFIX%elements` VALUES (25, 2, 1, 3, NULL, 'Objectives', '');
INSERT INTO `%PREFIX%elements` VALUES (26, 2, 1, 3, NULL, 'Materials', '');
INSERT INTO `%PREFIX%elements` VALUES (27, 2, 1, 3, NULL, 'Lesson Plan Text', '');
INSERT INTO `%PREFIX%elements` VALUES (28, 2, 2, 3, NULL, 'URL', '');
INSERT INTO `%PREFIX%elements` VALUES (29, 2, 2, 3, NULL, 'Event Type', '');
INSERT INTO `%PREFIX%elements` VALUES (30, 2, 1, 3, NULL, 'Participants', 'Names of individuals or groups participating in the event.');
INSERT INTO `%PREFIX%elements` VALUES (31, 2, 9, 3, NULL, 'Birth Date', '');
INSERT INTO `%PREFIX%elements` VALUES (32, 2, 2, 3, NULL, 'Birthplace', '');
INSERT INTO `%PREFIX%elements` VALUES (33, 2, 9, 3, NULL, 'Death Date', '');
INSERT INTO `%PREFIX%elements` VALUES (34, 2, 2, 3, NULL, 'Occupation', '');
INSERT INTO `%PREFIX%elements` VALUES (35, 2, 1, 3, NULL, 'Biographical Text', '');
INSERT INTO `%PREFIX%elements` VALUES (36, 2, 1, 3, NULL, 'Bibliography', '');
INSERT INTO `%PREFIX%elements` VALUES (37, 1, 2, 1, 8, 'Contributor', 'An entity responsible for making contributions to the resource. Examples of a Contributor include a person, an organization, or a service. Typically, the name of a Contributor should be used to indicate the entity.');
INSERT INTO `%PREFIX%elements` VALUES (38, 1, 2, 1, 15, 'Coverage', 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant. Spatial topic and spatial applicability may be a named place or a location specified by its geographic coordinates. Temporal topic may be a named period, date, or date range. A jurisdiction may be a named administrative entity or a geographic place to which the resource applies. Recommended best practice is to use a controlled vocabulary such as the Thesaurus of Geographic Names [TGN]. Where appropriate, named places or time periods can be used in preference to numeric identifiers such as sets of coordinates or date ranges.');
INSERT INTO `%PREFIX%elements` VALUES (39, 1, 2, 1, 4, 'Creator', 'An entity primarily responsible for making the resource. Examples of a Creator include a person, an organization, or a service. Typically, the name of a Creator should be used to indicate the entity.');
INSERT INTO `%PREFIX%elements` VALUES (40, 1, 2, 1, 7, 'Date', 'A point or period of time associated with an event in the lifecycle of the resource. Date may be used to express temporal information at any level of granularity. Recommended best practice is to use an encoding scheme, such as the W3CDTF profile of ISO 8601 [W3CDTF].');
INSERT INTO `%PREFIX%elements` VALUES (41, 1, 1, 1, 3, 'Description', 'An account of the resource. Description may include but is not limited to: an abstract, a table of contents, a graphical representation, or a free-text account of the resource.');
INSERT INTO `%PREFIX%elements` VALUES (42, 1, 2, 1, 11, 'Format', 'The file format, physical medium, or dimensions of the resource. Examples of dimensions include size and duration. Recommended best practice is to use a controlled vocabulary such as the list of Internet Media Types [MIME].');
INSERT INTO `%PREFIX%elements` VALUES (43, 1, 2, 1, 14, 'Identifier', 'An unambiguous reference to the resource within a given context. Recommended best practice is to identify the resource by means of a string conforming to a formal identification system.');
INSERT INTO `%PREFIX%elements` VALUES (44, 1, 2, 1, 12, 'Language', 'A language of the resource. Recommended best practice is to use a controlled vocabulary such as RFC 4646 [RFC4646].');
INSERT INTO `%PREFIX%elements` VALUES (45, 1, 2, 1, 6, 'Publisher', 'An entity responsible for making the resource available. Examples of a Publisher include a person, an organization, or a service. Typically, the name of a Publisher should be used to indicate the entity.');
INSERT INTO `%PREFIX%elements` VALUES (46, 1, 2, 1, 10, 'Relation', 'A related resource. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.');
INSERT INTO `%PREFIX%elements` VALUES (47, 1, 2, 1, 9, 'Rights', 'Information about rights held in and over the resource. Typically, rights information includes a statement about various property rights associated with the resource, including intellectual property rights.');
INSERT INTO `%PREFIX%elements` VALUES (48, 1, 2, 1, 5, 'Source', 'A related resource from which the described resource is derived. The described resource may be derived from the related resource in whole or in part. Recommended best practice is to identify the related resource by means of a string conforming to a formal identification system.');
INSERT INTO `%PREFIX%elements` VALUES (49, 1, 2, 1, 2, 'Subject', 'The topic of the resource. Typically, the subject will be represented using keywords, key phrases, or classification codes. Recommended best practice is to use a controlled vocabulary. To describe the spatial or temporal topic of the resource, use the Coverage element.');
INSERT INTO `%PREFIX%elements` VALUES (50, 1, 2, 1, 1, 'Title', 'A name given to the resource. Typically, a Title will be a name by which the resource is formally known.');
INSERT INTO `%PREFIX%elements` VALUES (51, 1, 2, 1, 13, 'Type', 'The nature or genre of the resource. Recommended best practice is to use a controlled vocabulary such as the DCMI Type Vocabulary [DCMITYPE]. To describe the file format, physical medium, or dimensions of the resource, use the Format element.');
INSERT INTO `%PREFIX%elements` VALUES (58, 3, 1, 4, 1, 'Additional Creator', '');
INSERT INTO `%PREFIX%elements` VALUES (59, 3, 1, 4, 2, 'Transcriber', '');
INSERT INTO `%PREFIX%elements` VALUES (60, 3, 1, 4, 3, 'Producer', '');
INSERT INTO `%PREFIX%elements` VALUES (61, 3, 1, 4, 4, 'Render Device', '');
INSERT INTO `%PREFIX%elements` VALUES (62, 3, 1, 4, 5, 'Render Details', '');
INSERT INTO `%PREFIX%elements` VALUES (63, 3, 10, 4, 6, 'Capture Date', '');
INSERT INTO `%PREFIX%elements` VALUES (64, 3, 1, 4, 7, 'Capture Device', '');
INSERT INTO `%PREFIX%elements` VALUES (65, 3, 1, 4, 8, 'Capture Details', '');
INSERT INTO `%PREFIX%elements` VALUES (66, 3, 1, 4, 9, 'Change History', '');
INSERT INTO `%PREFIX%elements` VALUES (67, 3, 1, 4, 10, 'Watermark', '');
INSERT INTO `%PREFIX%elements` VALUES (69, 3, 1, 4, 12, 'Encryption', '');
INSERT INTO `%PREFIX%elements` VALUES (70, 3, 1, 4, 13, 'Compression', '');
INSERT INTO `%PREFIX%elements` VALUES (71, 3, 1, 4, 14, 'Post Processing', '');
INSERT INTO `%PREFIX%elements` VALUES (72, 3, 4, 5, 1, 'Width', '');
INSERT INTO `%PREFIX%elements` VALUES (73, 3, 4, 5, 2, 'Height', '');
INSERT INTO `%PREFIX%elements` VALUES (74, 3, 4, 5, 3, 'Bit Depth', '');
INSERT INTO `%PREFIX%elements` VALUES (75, 3, 4, 5, 4, 'Channels', '');
INSERT INTO `%PREFIX%elements` VALUES (76, 3, 1, 5, 5, 'Exif String', '');
INSERT INTO `%PREFIX%elements` VALUES (77, 3, 1, 5, 6, 'Exif Array', '');
INSERT INTO `%PREFIX%elements` VALUES (78, 3, 1, 5, 7, 'IPTC String', '');
INSERT INTO `%PREFIX%elements` VALUES (79, 3, 1, 5, 8, 'IPTC Array', '');
INSERT INTO `%PREFIX%elements` VALUES (80, 3, 4, 6, 1, 'Bitrate', '');
INSERT INTO `%PREFIX%elements` VALUES (81, 3, 4, 6, 2, 'Duration', '');
INSERT INTO `%PREFIX%elements` VALUES (82, 3, 4, 6, 3, 'Sample Rate', '');
INSERT INTO `%PREFIX%elements` VALUES (83, 3, 1, 6, 4, 'Codec', '');
INSERT INTO `%PREFIX%elements` VALUES (84, 3, 4, 6, 5, 'Width', '');
INSERT INTO `%PREFIX%elements` VALUES (85, 3, 4, 6, 6, 'Height', '');
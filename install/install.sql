-- Unique Indices for Join tables (Doctrine not building these for some reason)
ALTER TABLE `items_tags` ADD UNIQUE `taguseritem` ( `tag_id` , `user_id` , `item_id` );
ALTER TABLE `exhibits_tags` ADD UNIQUE `tagexhibit` (`tag_id`, `exhibit_id`);

-- Types and Metafields

INSERT INTO `types` (id, name, description) VALUES (1, 'Document', 'A resource containing textual data.  Note that facsimiles or images of texts are still of the genre text.');
INSERT INTO `types` (id, name, description) VALUES (3, 'Moving Image', 'A series of visual representations that, when shown in succession, impart an impression of motion.');
INSERT INTO `types` (id, name, description) VALUES (4, 'Oral History', 'A resource containing historical information obtained in interviews with persons having firsthand knowledge.');
INSERT INTO `types` (id, name, description) VALUES (5, 'Sound', 'A resource whose content is primarily intended to be rendered as audio.');
INSERT INTO `types` (id, name, description) VALUES (6, 'Still Image', 'A static visual representation. Examples of still images are: paintings, drawings, graphic designs, plans and maps.  Recommended best practice is to assign the type "text" to images of textual materials.');
INSERT INTO `types` (id, name, description) VALUES (7, 'Website', 'A resource comprising of a web page or web pages and all related assets ( such as images, sound and video files, etc. ).');
INSERT INTO `types` (id, name, description) VALUES (8, 'Event', 'A non-persistent, time-based occurrence.  Metadata for an event provides descriptive information that is the basis for discovery of the purpose, location, duration, and responsible agents associated with an event. Examples include an exhibition, webcast, conference, workshop, open day, performance, battle, trial, wedding, tea party, conflagration.');



-- Additions
INSERT INTO `types` (id, name, description) VALUES (9, 'Email', '');
INSERT INTO `types` (id, name, description) VALUES (10, 'Lesson Plan', '');
INSERT INTO `types` (id, name, description) VALUES (11, 'Hyperlink', '');
INSERT INTO `types` (id, name, description) VALUES (12, 'Person', '');
INSERT INTO `types` (id, name, description) VALUES (13, 'Interactive Resource', 'A resource requiring interaction from the user to be understood, executed, or experienced. Examples include forms on Web pages, applets, multimedia learning objects, chat services, or virtual reality environments.');

INSERT INTO `metafields` (id, name, description) VALUES (1, 'Text', 'Any textual data included in the document.');
INSERT INTO `metafields` (id, name, description) VALUES (2, 'Interviewer', 'The person(s) performing the interview.');
INSERT INTO `metafields` (id, name, description) VALUES (3, 'Interviewee', 'The person(s) being interviewed.');
INSERT INTO `metafields` (id, name, description) VALUES (4, 'Location', 'The location of the interview.');
INSERT INTO `metafields` (id, name, description) VALUES (5, 'Transcription', 'Any written text transcribed from a sound.');
INSERT INTO `metafields` (id, name, description) VALUES (6, 'Local URL', 'The URL of the local directory containing all assets of the website.');

-- Additions
-- Document
INSERT INTO `metafields` (id, name, description) VALUES (7, 'Original Format', '');

-- Still Image
INSERT INTO `metafields` (id, name, description) VALUES (8, 'Resolution', '');
INSERT INTO `metafields` (id, name, description) VALUES (9, 'Dimensions (px)', '');
INSERT INTO `metafields` (id, name, description) VALUES (10, 'Physical Dimensions', '');

-- Moving Image
INSERT INTO `metafields` (id, name, description) VALUES (11, 'Duration', '');
INSERT INTO `metafields` (id, name, description) VALUES (12, 'Compression', '');
INSERT INTO `metafields` (id, name, description) VALUES (13, 'Producer', '');
INSERT INTO `metafields` (id, name, description) VALUES (14, 'Director', '');

-- Sound
INSERT INTO `metafields` (id, name, description) VALUES (15, 'Bit Rate/Frequency', '');

-- Oral History
INSERT INTO `metafields` (id, name, description) VALUES (16, 'Time Summary', '');

-- Email
INSERT INTO `metafields` (id, name, description) VALUES (17, 'Email Body', '');
INSERT INTO `metafields` (id, name, description) VALUES (18, 'Subject', '');
INSERT INTO `metafields` (id, name, description) VALUES (19, 'From', '');
INSERT INTO `metafields` (id, name, description) VALUES (20, 'To', '');
INSERT INTO `metafields` (id, name, description) VALUES (21, 'CC', '');
INSERT INTO `metafields` (id, name, description) VALUES (22, 'BCC', '');
INSERT INTO `metafields` (id, name, description) VALUES (23, 'Number of Attachments', '');

-- Lesson Plan
INSERT INTO `metafields` (id, name, description) VALUES (24, 'Standards', '');
INSERT INTO `metafields` (id, name, description) VALUES (25, 'Objectives', '');
INSERT INTO `metafields` (id, name, description) VALUES (26, 'Materials', '');
INSERT INTO `metafields` (id, name, description) VALUES (27, 'Lesson Plan Text', '');

-- Hyperlink
INSERT INTO `metafields` (id, name, description) VALUES (28, 'URL', '');

-- Event
INSERT INTO `metafields` (id, name, description) VALUES (29, 'Event Type', '');
INSERT INTO `metafields` (id, name, description) VALUES (30, 'Participants', '');

-- Person
INSERT INTO `metafields` (id, name, description) VALUES (31, 'Birth Date', '');
INSERT INTO `metafields` (id, name, description) VALUES (32, 'Birthplace', '');
INSERT INTO `metafields` (id, name, description) VALUES (33, 'Death Date', '');
INSERT INTO `metafields` (id, name, description) VALUES (34, 'Occupation', '');
INSERT INTO `metafields` (id, name, description) VALUES (35, 'Biographical Text', '');
INSERT INTO `metafields` (id, name, description) VALUES (36, 'Bibliography', '');

-- Insert the types_metafields relationships

INSERT INTO `types_metafields` (`id`, `type_id`, `metafield_id`) VALUES (1, 1, 7),
(2, 1, 1),
(3, 6, 7),
(4, 6, 8),
(5, 6, 9),
(6, 6, 10),
(7, 3, 7),
(8, 3, 11),
(9, 3, 12),
(10, 3, 13),
(11, 3, 14),
(12, 3, 5),
(13, 5, 7),
(14, 5, 11),
(15, 5, 15),
(16, 5, 5),
(17, 4, 7),
(18, 4, 11),
(19, 4, 15),
(20, 4, 5),
(21, 4, 2),
(22, 4, 3),
(23, 4, 4),
(24, 4, 16),
(25, 9, 17),
(26, 9, 18),
(27, 9, 20),
(28, 9, 19),
(29, 9, 21),
(30, 9, 22),
(31, 9, 23),
(32, 10, 24),
(33, 10, 25),
(34, 10, 26),
(35, 10, 11),
(36, 10, 27),
(37, 7, 6),
(38, 11, 28),
(39, 8, 29),
(40, 8, 30),
(41, 8, 11),
(42, 12, 31),
(43, 12, 32),
(44, 12, 33),
(45, 12, 34),
(46, 12, 35),
(47, 12, 36);

-- CREATE TABLE `items_fulltext` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,`item_id` INT(11) UNSIGNED NOT NULL, `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,FULLTEXT (`text`)) ENGINE = MYISAM;
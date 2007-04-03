INSERT INTO `types` (name, description) VALUES ('Document', 'A resource containing textual data.  Note that facsimiles or images of texts are still of the genre text.');
INSERT INTO `types` (name, description) VALUES ('Interactive Resource', 'A resource which requires interaction from the user to be understood, executed, or experienced.');
INSERT INTO `types` (name, description) VALUES ('Moving Image', 'A series of visual representations that, when shown in succession, impart an impression of motion.');
INSERT INTO `types` (name, description) VALUES ('Oral History', 'A resource containing historical information obtained in interviews with persons having firsthand knowledge.');
INSERT INTO `types` (name, description) VALUES ('Sound', 'A resource whose content is primarily intended to be rendered as audio.');
INSERT INTO `types` (name, description) VALUES ('Still Image', 'A static visual representation. Examples of still images are: paintings, drawings, graphic designs, plans and maps.  Recommended best practice is to assign the type "text" to images of textual materials.');
INSERT INTO `types` (name, description) VALUES ('Website', 'A resource comprising of a web page or web pages and all related assets ( such as images, sound and video files, etc. ).');
INSERT INTO `types` (name, description) VALUES ('Event', 'A non-persistent, time-based occurrence.  Metadata for an event provides descriptive information that is the basis for discovery of the purpose, location, duration, and responsible agents associated with an event. Examples include an exhibition, webcast, conference, workshop, open day, performance, battle, trial, wedding, tea party, conflagration.');

INSERT INTO `metafields` (name, description) VALUES ('Text', 'Any textual data included in the document.');
INSERT INTO `metafields` (name, description) VALUES ('Oral History Transcription', 'Any written text transcribed from or during the interview.');
INSERT INTO `metafields` (name, description) VALUES ('Interviewer', 'The person(s) performing the interview.');
INSERT INTO `metafields` (name, description) VALUES ('Interviewee', 'The person(s) being interviewed.');
INSERT INTO `metafields` (name, description) VALUES ('Location', 'The location of the interview.');
INSERT INTO `metafields` (name, description) VALUES ('Sound Transcription', 'Any written text transcribed from the sound.');
INSERT INTO `metafields` (name, description) VALUES ('Local URL', 'The URL of the local directory containing all assets of the website.');

INSERT INTO `types_metafields` (type_id, metafield_id) VALUES (1, 1);
INSERT INTO `types_metafields` (type_id, metafield_id) VALUES (4, 2);
INSERT INTO `types_metafields` (type_id, metafield_id) VALUES (4, 3);
INSERT INTO `types_metafields` (type_id, metafield_id) VALUES (4, 4);
INSERT INTO `types_metafields` (type_id, metafield_id) VALUES (4, 5);
INSERT INTO `types_metafields` (type_id, metafield_id) VALUES (5, 6);
INSERT INTO `types_metafields` (type_id, metafield_id) VALUES (7, 7);



CREATE TABLE `items_fulltext` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,`item_id` INT(11) UNSIGNED NOT NULL, `text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,FULLTEXT (`text`)) ENGINE = MYISAM;
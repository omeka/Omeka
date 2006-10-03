/**
 * Covered under the GPL license: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2006:
 * George Mason University
 * Center for History of New Media,
 * State of Virginia
 * 
 * @author Nate Agrin
 * @contributors Jim Safley, Josh Greenburg, Tom Scheinfeldt
 * @copywrite GPL http://www.gnu.org/licenses/gpl.txt
 */

-- Necessary to avoid foreign key errors when reloading
SET FOREIGN_KEY_CHECKS = 0;

-- 
-- Objects
--

DROP TABLE IF EXISTS objects;
CREATE TABLE objects (

# Dublin Core
	# Identifier
	object_id					int(11)		UNSIGNED NOT NULL auto_increment,

	# Title
	object_title				tinytext	NOT NULL,
	
	# Publisher
	object_publisher			text		NOT NULL,

	# Language
	object_language				tinytext	NOT NULL,
	
	# Rights
	object_rights				text		NOT NULL,
	
	# Description
	object_description			text		NOT NULL,
	
	# Date
	object_date					tinytext	NOT	NULL,
	
	# Relation
	object_relation				text		NOT NULL,

	# Source
	object_source				text		NOT NULL,
	
	# Subject					
	object_subject				tinytext	NOT NULL,
	
	# Type - aka - KJV object type metadata
	type_id					int(11)		UNSIGNED NULL,
	
	# Creator
	object_creator				text		NOT NULL,
	
	# Additional creator info
	object_additional_creator	text		NOT NULL,
	
	# Contributor
	#contributor_id				int(11)		UNSIGNED NULL,
	
	# Creator
	#creator_id					int(11)		UNSIGNED NULL,
	
	# Creator other
	#creator_other				text		NOT NULL,
	
	# Source
	collection_id				int(11)		UNSIGNED NULL,
	
	# Transcriber
	user_id						int(11)		UNSIGNED NULL,
	
# Dublin Core End
	
#	Other meta data
	object_coverage				text		NOT NULL,
	
	object_added				timestamp	NULL default NULL,
	object_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	
#	Object Featured
	object_featured				int(1)		NOT NULL DEFAULT '0',
	
#	Object Published
	object_public			BOOL 		NOT NULL DEFAULT '0', 
	PRIMARY KEY	(object_id),
	INDEX		(type_id),
	#INDEX		(contributor_id),
	#INDEX		(creator_id),
	INDEX		(collection_id),
	INDEX		(user_id)

) ENGINE=innodb DEFAULT CHARSET=utf8;

CREATE TRIGGER object_added BEFORE INSERT ON objects
FOR EACH ROW
	SET NEW.object_added = NOW();

DROP TABLE IF EXISTS objectsTotal;
CREATE TABLE objectsTotal (
	total	int(11)		NOT NULL default 0
) DEFAULT CHARSET=utf8;
INSERT INTO objectsTotal (total) VALUES (0);

CREATE TRIGGER objects_plus AFTER INSERT ON objects
	FOR EACH ROW
		UPDATE objectsTotal SET total = total + 1;

CREATE TRIGGER objects_minus AFTER DELETE ON objects
	FOR EACH ROW
		UPDATE objectsTotal SET total = total - 1;


-- --------------------------------------------------------

--
-- KJV Object Types
--

DROP TABLE IF EXISTS types;
CREATE TABLE types (
	type_id							int(11)		UNSIGNED NOT NULL auto_increment,
	type_name						tinytext	NOT NULL,
	type_description				text		NOT NULL,
	type_active						tinyint(1)	UNSIGNED NOT NULL default '1', -- This may be switched to 0

	PRIMARY KEY  (type_id)

)   ENGINE = innodb DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- kjvObjectTypes_metaFields
-- 



DROP TABLE IF EXISTS metafields;
CREATE TABLE metafields (
	metafield_id				int(11)			UNSIGNED NOT NULL auto_increment,
	metafield_name				varchar(100)	NOT NULL,
	metafield_description		text			NOT NULL,

	PRIMARY KEY (metafield_id)

) ENGINE = innodb DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS types_metafields;
CREATE TABLE types_metafields (
	type_id					int(11) UNSIGNED NOT NULL,
	metafield_id					int(11) UNSIGNED NOT NULL,
	INDEX (type_id),
	INDEX (metafield_id),
	FOREIGN KEY (type_id) REFERENCES types(type_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (metafield_id) REFERENCES metafields(metafield_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = innodb DEFAULT CHARSET=utf8;

--
-- May need to keep this table MyISAM for full-text searches
--

DROP TABLE IF EXISTS metatext;
CREATE TABLE metatext (
	metatext_id					int(11)			UNSIGNED NOT NULL	auto_increment,
	metafield_id				int(11)			UNSIGNED NOT NULL,
	object_id					int(11)			UNSIGNED NOT NULL,
	metatext_text				text			NOT NULL,

	PRIMARY KEY (metatext_id),
	INDEX		(metafield_id),
	INDEX		(object_id),
	FOREIGN KEY (metafield_id) REFERENCES metafields(metafield_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE
	
) ENGINE=innodb DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Object Contributors
--
DROP TABLE IF EXISTS contributors;
CREATE TABLE contributors (
	contributor_id						int(11)								UNSIGNED NOT NULL auto_increment,
	contributor_first_name				varchar(100)						NOT NULL,
	contributor_middle_name				varchar(100)						NOT NULL,
	contributor_last_name				varchar(100)						NOT NULL,
	contributor_email					varchar(100)						NOT NULL,
	contributor_phone					varchar(40)							NOT NULL,
	contributor_birth_year				int(4)								NOT NULL,
	contributor_gender					enum( 'male', 'female', 'unknown' )	default 'unknown',
	contributor_race					enum( 'Asian/Pacific', 'Islander', 'African American', 'Hispanic', 'Native American / Indian', 'White', 'Other', 'unknown' )	default 'unknown',
	contributor_race_other				tinytext							NOT NULL,
	contributor_contact_consent			enum( 'yes', 'no', 'unknown' )		default 'unknown',

	contributor_fax						varchar(14)		NOT NULL,
	contributor_address					varchar(100)	NOT NULL,
	contributor_city					varchar(16)		NOT NULL,
	contributor_state					varchar(16)		NOT NULL,
	contributor_zipcode					varchar(10)		NOT NULL,
	contributor_occupation				varchar(255)	NOT NULL,
-- Specific to HDMB, remove if necessary
	contributor_institution				varchar(255)    NOT NULL,
	PRIMARY KEY (contributor_id)

) ENGINE=innodb DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


-- 
-- Table structure for table collections
-- 
DROP TABLE IF EXISTS collections;
CREATE TABLE collections (
	collection_id			int(11)		UNSIGNED NOT NULL auto_increment,
	collection_name			tinytext	NOT NULL,
	collection_description	text		NOT NULL,
	collection_active		tinyint(1)	UNSIGNED NOT NULL default '0',
	collection_featured		tinyint(1)	UNSIGNED NOT NULL default '0',
	collection_collector	text		NOT NULL,

	PRIMARY KEY  (collection_id)

) ENGINE=innodb DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Files
--

DROP TABLE IF EXISTS files;
CREATE TABLE files (
	file_id						int(11)		UNSIGNED NOT NULL auto_increment,

#	Dublin core
	file_title					varchar(255)	NOT NULL,

	file_publisher				text			NOT NULL,
	file_language				varchar(255)	NOT NULL,
	file_relation				text			NOT NULL,
	file_rights					text			NOT NULL,
	file_description			text			NOT NULL,
	file_date					tinytext		NOT NULL,
	file_source					text			NOT NULL,
	file_subject				varchar(255)	NOT NULL,
	file_creator				text			NOT NULL,
	file_additional_creator		text			NOT NULL,

#	Coverage
	file_coverage				text		NOT NULL,

#	Dublin core referenced in other tables
	object_id					int(11)		UNSIGNED NULL,
	#contributor_id				int(11)		UNSIGNED NULL,

#	File preservation and digitization metadata
	file_transcriber			text	NOT NULL,
	file_producer				text	NOT NULL,
	file_render_device			text	NOT NULL,
	file_render_details			text	NOT NULL,
	file_capture_date			timestamp	NOT NULL default '0000-00-00 00:00:00',
	file_capture_device			text	NOT NULL,
	file_change_history			text	NOT NULL,
	file_watermark				text	NOT NULL,
	file_authentication			text	NOT NULL,
	file_encryption				text	NOT NULL,
	file_compression			text	NOT NULL,
	file_post_processing		text	NOT NULL,

#	Physical file related data
	file_archive_filename		tinytext	NOT NULL,
	file_fullsize_filename		tinytext	NOT NULL,
	file_original_filename		tinytext	NOT NULL,
	file_thumbnail_name			tinytext	NOT NULL,
	file_size					int(11)		UNSIGNED NOT NULL default '0',	
	file_mime_browser			tinytext	NOT NULL,
	file_mime_php				tinytext	NOT NULL,
	file_mime_os				tinytext	NOT NULL,
	file_type_os				tinytext	NOT NULL,
	
	file_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	file_added					timestamp	NULL default NULL,

  PRIMARY KEY  (file_id),
	INDEX		(object_id),
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE
	#FOREIGN KEY (contributor_id) REFERENCES contributors(contributor_id) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=innodb DEFAULT CHARSET=utf8;

CREATE TRIGGER file_added BEFORE INSERT ON files
FOR EACH ROW
	SET NEW.file_added = NOW();

-- --------------------------------------------------------

--
-- Users
--

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id				int(11)		UNSIGNED NOT NULL auto_increment,
  user_username			varchar(30) NOT NULL,
  user_password			varchar(40) NOT NULL,
  user_first_name		tinytext	NOT NULL,
  user_last_name		tinytext	NOT NULL,
  user_email			tinytext	NOT NULL,
  user_institution		text		NOT NULL,
  user_permission_id	int(11)		UNSIGNED NOT NULL default '100',
  user_active			int(1)		UNSIGNED NOT NULL default '0',
	contributor_id		int(11)		UNSIGNED NULL,

  PRIMARY KEY  (user_id)

) ENGINE=innodb DEFAULT CHARSET=utf8;

--
-- Geolocation
--

DROP TABLE IF EXISTS location;
CREATE TABLE location(
	location_id		int(11)			NOT NULL auto_increment,
	object_id		int(11)			UNSIGNED NOT NULL,
	latitude		double			NULL,
	longitude		double			NULL,
	address			varchar(255)	NOT NULL,
	zipcode			varchar(10)		NOT NULL,
	zoomLevel		varchar(40)		NOT NULL,
	mapType			varchar(100)	NOT NULL,
	cleanAddress	varchar(200)	NOT NULL,

	PRIMARY KEY (location_id),
	INDEX		(object_id),
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=innodb DEFAULT CHARSET=utf8;

--
-- Tags
--

DROP TABLE IF EXISTS tags;
CREATE TABLE tags(
	tag_id			int(11)			UNSIGNED NOT NULL auto_increment,
	tag_name		varchar(255)	NOT NULL,
	PRIMARY KEY(tag_id)
) ENGINE=innodb DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS objects_tags;
CREATE TABLE objects_tags(
	object_id		int(11)			UNSIGNED NOT NULL,
	tag_id			int(11)			UNSIGNED NOT NULL,
	user_id			int(11)			UNSIGNED NOT NULL,
	INDEX	(object_id),
	INDEX	(tag_id),
	INDEX	(user_id),
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (tag_id) REFERENCES tags(tag_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=innodb DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS objects_favorites;
CREATE TABLE objects_favorites(
	object_id		int(11)			UNSIGNED NOT NULL,
	user_id			int(11)			UNSIGNED NOT NULL,
	fav_added		timestamp		NULL,
	INDEX	(object_id),
	INDEX	(user_id),
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = innodb DEFAULT CHARSET=utf8;

CREATE TRIGGER fav_added BEFORE INSERT ON objects_favorites
FOR EACH ROW
	SET NEW.fav_added = NOW();



ALTER TABLE objects ADD CONSTRAINT `type_key` FOREIGN KEY (type_id) REFERENCES types(type_id) ON DELETE SET NULL ON UPDATE CASCADE;
#ALTER TABLE objects ADD CONSTRAINT `contributor_key` FOREIGN KEY (contributor_id) REFERENCES contributors(contributor_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE objects ADD CONSTRAINT `collection_key` FOREIGN KEY (collection_id) REFERENCES collections(collection_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE objects ADD CONSTRAINT `user_key` FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO `types` (`type_id`,  `type_name`, `type_description`, `type_active`) VALUES
(3,  'Document', 'A resource containing textual data.  Note that facsimiles or images of texts are still of the genre text.', 1),
(4,  'Email', 'A resource containing textual messages and binary attachments sent electronically from one person to another or one person to many people.', 1),
(5,  'Interactive Resource', 'A resource which requires interaction from the user to be understood, executed, or experienced.', 1),
(6,  'Moving Image', 'A series of visual representations that, when shown in succession, impart an impression of motion.', 1),
(9,  'Oral History', 'A resource containing historical information obtained in interviews with persons having firsthand knowledge.', 1),
(10,  'Sound', 'A resource whose content is primarily intended to be rendered as audio.', 1),
(11,  'Still Image', 'A static visual representation. Examples of still images are: paintings, drawings, graphic designs, plans and maps.  Recommended best practice is to assign the type "text" to images of textual materials.', 1),
(12,  'Web Page', 'A resource intended for publication on the World Wide Web using hypertext markup language.', 1),
(13,  'Website', 'A resource comprising of a web page or web pages and all related assets ( such as images, sound and video files, etc. ).', 1);


INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES 
(5, 'Text', 'Any textual data included in the document.'),
(6, 'Email Body', 'The main body of the email, including all replied and forwarded text and headers.'),
(7, 'Subject Line', 'The content of the subject line of the email.'),
(8, 'From', 'The name and email address of the person sending the email.'),
(9, 'To', 'The name(s) and email address(es) of the person to whom the email was sent.'),
(10, 'Cc', 'The name(s) and email address(es) of the person to whom the email was carbon copied.'),
(11, 'Bcc', 'The name(s) and email address(es) of the person to whom the email was blind carbon copied.'),
(12, 'Number of Attachments', 'The number of attachments to the email.'),
(19, 'Oral History Transcription', 'Any written text transcribed from or during the interview.'),
(20, 'Interviewer', 'The person(s) performing the interview.'),
(21, 'Interviewee', 'The person(s) being interviewed.'),
(22, 'Location', 'The location of the interview.'),
(26, 'Sound Transcription', 'Any written text transcribed from the sound.'),
(31, 'HTML', 'The hypertext markup language used for building the web page.'),
(32, 'Local URL', 'The URL of the local directory containing all assets of the website.');

INSERT INTO `types_metafields` (`type_id`, `metafield_id`) VALUES
(3, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(4, 11),
(4, 12),
(9, 19),
(9, 20),
(9, 21),
(9, 22),
(10, 26),
(12, 31),
(13, 32);

INSERT INTO users (`user_username`, `user_password`, `user_permission_id`, `user_active`) VALUES ('super', SHA1('super'), 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
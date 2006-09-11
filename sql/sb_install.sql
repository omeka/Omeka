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

-- 
-- Objects
--

DROP TABLE IF EXISTS objects;
CREATE TABLE objects (

# Dublin Core
	# Identifier
	object_id					int(11)		UNSIGNED NOT NULL auto_increment,

	# Title
	object_title				tinytext	NULL,
	
	# Publisher
	object_publisher			text		NULL,

	# Language
	object_language				tinytext	NULL,
	
	# Rights
	object_rights				text		NULL,
	
	# Description
	object_description			text		NULL,
	
	# Date
	object_date					timestamp	NULL,
	
	# Status
	object_status				enum( 'review', 'approved', 'rejected', 'notyet', 'moreinfo' )	NOT NULL default 'notyet',
	
	# Relation
	object_relation				text		NULL,

	# Type - aka - KJV object type metadata
	category_id					int(11)		UNSIGNED NULL,
	
	# Contributor
	contributor_id				int(11)		UNSIGNED NULL,
	
	# Creator
	creator_id					int(11)		UNSIGNED NULL,
	
	# Creator other
	creator_other				text		NULL,
	
	# Source
	collection_id				int(11)		UNSIGNED NULL,
	
	# Transcriber
	user_id						int(11)		UNSIGNED NULL,
	
# Dublin Core End
	
#	Other meta data
	object_coverage_start		timestamp		NULL,
	object_coverage_end			timestamp		NULL,

#	Object consents
	object_contributor_consent	enum( 'yes', 'unsure', 'restrict', 'no', 'unknown' ) 	NOT NULL default 'unknown',
	object_contributor_posting	enum( 'yes', 'no', 'anonymously', 'unknown') 			NOT NULL default 'unknown',
	
	object_added				timestamp	NULL,
	object_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	
#	Object Featured
	object_featured				int(1)		NOT NULL DEFAULT '0',
	
	PRIMARY KEY	(object_id),
	INDEX		(category_id),
	INDEX		(contributor_id),
	INDEX		(creator_id),
	INDEX		(collection_id),
	INDEX		(user_id)

) ENGINE=innodb DEFAULT CHARSET=latin1;

CREATE TRIGGER object_added BEFORE INSERT ON objects
FOR EACH ROW
	SET NEW.object_added = NOW();

DROP TABLE IF EXISTS objectsTotal;
CREATE TABLE objectsTotal (
	total	int(11)		NOT NULL default 0
);
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

DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
	category_id							int(11)		UNSIGNED NOT NULL auto_increment,
	category_name						tinytext	NOT NULL,
	category_description				text		NULL,
	category_active						tinyint(1)	UNSIGNED NOT NULL default '1', -- This may be switched to 0

	PRIMARY KEY  (category_id)

)   ENGINE = innodb DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- kjvObjectTypes_metaFields
-- 



DROP TABLE IF EXISTS metafields;
CREATE TABLE metafields (
	metafield_id				int(11)			UNSIGNED NOT NULL auto_increment,
	metafield_name				varchar(100)	NOT NULL,
	metafield_description		text			NULL,

	PRIMARY KEY (metafield_id)

) ENGINE = innodb DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS categories_metafields;
CREATE TABLE categories_metafields (
	category_id					int(11) UNSIGNED NOT NULL,
	metafield_id					int(11) UNSIGNED NOT NULL,
	INDEX (category_id),
	INDEX (metafield_id),
	FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (metafield_id) REFERENCES metafields(metafield_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = innodb DEFAULT CHARSET=latin1;

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
	
) ENGINE=innodb DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Object Contributors
--
DROP TABLE IF EXISTS contributors;
CREATE TABLE contributors (
	contributor_id						int(11)								UNSIGNED NOT NULL auto_increment,
	contributor_first_name				varchar(100)						NULL,
	contributor_middle_name				varchar(100)						NULL,
	contributor_last_name				varchar(100)						NULL,
	contributor_email					varchar(100)						NULL,
	contributor_phone					varchar(40)							NULL,
	contributor_birth_year				int(4)								NULL,
	contributor_gender					enum( 'male', 'female', 'unknown' )	default 'unknown',
	contributor_race					enum( 'Asian/Pacific', 'Islander', 'African American', 'Hispanic', 'Native American / Indian', 'White', 'Other', 'unknown' )	default 'unknown',
	contributor_race_other				tinytext							NULL,
	contributor_contact_consent			enum( 'yes', 'no', 'unknown' )		default 'unknown',

	contributor_fax						varchar(14)		NULL,
	contributor_address					varchar(100)	NULL,
	contributor_city					varchar(16)		NULL,
	contributor_state					varchar(16)		NULL,
	contributor_zipcode					varchar(10)		NULL,
	contributor_occupation				varchar(255)	NULL,
	contributor_institution				varchar(255)    NULL,
	
	PRIMARY KEY (contributor_id)

) ENGINE=innodb DEFAULT CHARSET=latin1;

-- --------------------------------------------------------


-- 
-- Table structure for table collections
-- 
DROP TABLE IF EXISTS collections;
CREATE TABLE collections (
	collection_id			int(11)		UNSIGNED NOT NULL auto_increment,
	collection_name			tinytext	NOT NULL,
	collection_description	text		NULL,
	collection_active		tinyint(1)	UNSIGNED NOT NULL default '0',
	collection_featured		tinyint(1)	UNSIGNED NOT NULL default '0',
	collection_collector	text		NULL,
	collection_parent		int(11)		NULL,

	PRIMARY KEY  (collection_id)

) ENGINE=innodb DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Files
--

DROP TABLE IF EXISTS files;
CREATE TABLE files (
	file_id						int(11)		UNSIGNED NOT NULL auto_increment,

#	Dublin core
	file_title					varchar(255)	NULL,

	file_publisher				text			NULL,
	file_language				varchar(255)	NULL,
	file_relation				text			NULL,
	file_rights					text			NULL,
	file_description			text			NULL,
	file_date					timestamp		NULL,

#	Coverage
	file_coverage_start			timestamp	NULL,
	file_coverage_end			timestamp	NULL,

#	Dublin core referenced in other tables
	object_id					int(11)		UNSIGNED NULL,
	contributor_id				int(11)		UNSIGNED NULL,

#	File preservation and digitization metadata
	file_transcriber			text	NULL,
	file_producer				text	NULL,
	file_render_device			text	NULL,
	file_render_details			text	NULL,
	file_capture_date			timestamp	NULL,
	file_capture_device			text	NULL,
	file_change_history			text	NULL,
	file_watermark				text	NULL,
	file_authentication			text	NULL,
	file_encryption				text	NULL,
	file_compression			text	NULL,
	file_post_processing		text	NULL,

#	Physical file related data
	file_archive_filename		tinytext	NOT NULL,
	file_original_filename		tinytext	NOT NULL,
	file_thumbnail_name			tinytext	NOT NULL,
	file_size					int(11)		UNSIGNED NOT NULL default '0',	
	file_mime_browser			tinytext	NULL,
	file_mime_php				tinytext	NULL,
	file_mime_os				tinytext	NULL,
	file_type_os				tinytext	NULL,
	
	file_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	file_added					timestamp	NOT NULL default '0000-00-00 00:00:00',

  PRIMARY KEY  (file_id),
	INDEX		(object_id),
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (contributor_id) REFERENCES contributors(contributor_id) ON DELETE SET NULL ON UPDATE CASCADE

) ENGINE=innodb DEFAULT CHARSET=latin1;

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
  user_first_name		tinytext	NULL,
  user_last_name		tinytext	NULL,
  user_email			tinytext	NULL,
  user_institution		text		NULL,
  user_permission_id	int(11)		UNSIGNED NOT NULL default '100',
  user_active			int(1)		UNSIGNED NOT NULL default '0',
	contributor_id		int(11)		UNSIGNED NULL,

  PRIMARY KEY  (user_id)

) ENGINE=innodb DEFAULT CHARSET=latin1;

--
-- Geolocation
--

DROP TABLE IF EXISTS location;
CREATE TABLE location(
	location_id		int(11)			NOT NULL auto_increment,
	object_id		int(11)			UNSIGNED NOT NULL,
	latitude		double			NULL,
	longitude		double			NULL,
	address			varchar(255)	NULL,
	zipcode			varchar(10)		NULL,
	zoomLevel		varchar(40)		NULL,
	mapType			varchar(100)	NULL,
	cleanAddress	varchar(200)	NULL,

	PRIMARY KEY (location_id),
	INDEX		(object_id),
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE

) ENGINE=innodb DEFAULT CHARSET=latin1;

--
-- Tags
--

DROP TABLE IF EXISTS tags;
CREATE TABLE tags(
	tag_id			int(11)			UNSIGNED NOT NULL auto_increment,
	tag_name		varchar(255)	NOT NULL,
	PRIMARY KEY(tag_id)
) ENGINE=innodb DEFAULT CHARSET=latin1;

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
) ENGINE=innodb DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS objects_favorites;
CREATE TABLE objects_favorites(
	object_id		int(11)			UNSIGNED NOT NULL,
	user_id			int(11)			UNSIGNED NOT NULL,
	fav_added		timestamp		NULL,
	INDEX	(object_id),
	INDEX	(user_id),
	FOREIGN KEY (object_id) REFERENCES objects(object_id) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = innodb DEFAULT CHARSET=latin1;

CREATE TRIGGER fav_added BEFORE INSERT ON objects_favorites
FOR EACH ROW
	SET NEW.fav_added = NOW();



ALTER TABLE objects ADD CONSTRAINT `category_key` FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE objects ADD CONSTRAINT `contributor_key` FOREIGN KEY (contributor_id) REFERENCES contributors(contributor_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE objects ADD CONSTRAINT `collection_key` FOREIGN KEY (collection_id) REFERENCES collections(collection_id) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE objects ADD CONSTRAINT `user_key` FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO `categories` (`category_id`,  `category_name`, `category_description`, `category_active`) VALUES (2,  'Blog', 'A resource containing frequent and chronological comments and throughts published on the web.', 1),
(3,  'Document', 'A resource containing textual data.  Note that facsimiles or images of texts are still of the genre text.', 1),
(4,  'Email', 'A resource containing textual messages and binary attachments sent electronically from one person to another or one person to many people.', 1),
(5,  'Interactive Resource', 'A resource which requires interaction from the user to be understood, executed, or experienced.', 1),
(6,  'Moving Image', 'A series of visual representations that, when shown in succession, impart an impression of motion.', 1),
(7,  'Online File', 'A file and accompanying metadata contributed to JWA through the KJV contribution form.', 1),
(8,  'Online Text', 'A story, email, blog, or any other textual data contributed to JWA through the KJV contribution form.', 1),
(9,  'Oral History', 'A resource containing historical information obtained in interviews with persons having firsthand knowledge.', 1),
(10,  'Sound', 'A resource whose content is primarily intended to be rendered as audio.', 1),
(11,  'Still Image', 'A static visual representation. Examples of still images are: paintings, drawings, graphic designs, plans and maps.  Recommended best practice is to assign the type "text" to images of textual materials.', 1),
(12,  'Web Page', 'A resource intended for publication on the World Wide Web using hypertext markup language.', 1),
(13,  'Website', 'A resource comprising of a web page or web pages and all related assets ( such as images, sound and video files, etc. ).', 1);


INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (2, 'Body', 'The main body of the blog post.'),
(3, 'Comments', 'Any comments made in response to the blog post, including the date.'),
(4, 'Trackbacks', 'A list of all blog postings that have referenced this blog post.'),
(5, 'Text', 'Any textual data included in the document.'),
(6, 'Email Body', 'The main body of the email, including all replied and forwarded text and headers.'),
(7, 'Subject Line', 'The content of the subject line of the email.'),
(8, 'From', 'The name and email address of the person sending the email.'),
(9, 'To', 'The name(s) and email address(es) of the person to whom the email was sent.'),
(10, 'Cc', 'The name(s) and email address(es) of the person to whom the email was carbon copied.'),
(11, 'Bcc', 'The name(s) and email address(es) of the person to whom the email was blind carbon copied.'),
(12, 'Number of Attachments', 'The number of attachments to the email.'),
(13, 'Interactive Resource Duration', 'The length in time of the interactive resource.'),
(14, 'Moving Image Duration', 'The lentgh of time of the moving image.'),
(15, 'Moving Image Resolution', 'The resolution of the moving image determined by pixel dimensions, pixels per inch or dots per inch.'),
(16, 'Moving Image Bit Depth', 'The bit depth of the moving image.'),
(17, 'Moving Image Width', 'The width of the moving image at full size.'),
(18, 'Moving Image Height', 'The height of the moving image at full size.'),
(19, 'Oral History Transcription', 'Any written text transcribed from or during the interview.'),
(20, 'Interviewer', 'The person(s) performing the interview.'),
(21, 'Interviewee', 'The person(s) being interviewed.'),
(22, 'Location', 'The location of the interview.'),
(23, 'Oral History Duration', 'The length of time of the interview.'),
(24, 'Sample Rate', 'The number of samples recorded per second.  Sample rates are measured in Hz or kHz.'),
(25, 'Bit Depth', 'The number of bits used to represent each sample in an audio file, determining the accuracy of the sample.'),
(26, 'Sound Transcription', 'Any written text transcribed from the sound.'),
(27, 'Sound Duration', 'The length of time of the sound.'),
(28, 'Resolution', 'The resolution of the still image'),
(29, 'Still Image Width', 'The width of the still image at full size.'),
(30, 'Still Image Height', 'The height of the still image at full size.'),
(31, 'HTML', 'The hypertext markup language used for building the web page.'),
(32, 'Local URL', 'The URL of the local directory containing all assets of the website.');

INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (2, 2),
(2, 3),
(2, 4),
(3, 5),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
(4, 11),
(4, 12),
(5, 13),
(6, 14),
(6, 15),
(6, 16),
(6, 17),
(6, 18),
(8, 5),
(9, 19),
(9, 20),
(9, 21),
(9, 22),
(9, 23),
(9, 24),
(9, 25),
(10, 26),
(10, 27),
(10, 24),
(10, 25),
(11, 28),
(11, 25),
(11, 29),
(11, 30),
(12, 31),
(13, 32);
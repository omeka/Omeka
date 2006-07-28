-- 
-- Objects
--

/*
	Dublin Core Mapping

	Title			object_title
	Creator			object_creator		(creator_id)
	Publisher		object_publisher	(publisher_id)
	Format			object_format		(format_id)
	Identifier		object_id
	Source			collection_id
	Language		language_id
	Relation		object_parent_id
	Coverage		coverage_id
	Rights			object_rights
	Subject			tags????
	Description		object_description
	Date			object_date
*/

DROP TABLE IF EXISTS objects;
CREATE TABLE objects (

	# Object Identifier
	object_id					int(11)		UNSIGNED NOT NULL auto_increment,

#	Dublin core
	object_title				tinytext	NOT NULL,
	object_creator				text		NOT NULL,
	object_publisher			text		NOT NULL,
	object_format				text		NOT NULL,
	object_identifier			text		NOT NULL,
	object_source				text		NOT NULL,
	object_language				text		NOT NULL,
	object_relation				text		NOT NULL,
	object_coverage				text		NOT NULL,
	object_rights				text		NOT NULL,
	object_subject				text		NOT NULL,
	object_description			text		NOT NULL,
	object_date					text		NOT NULL,
	
#	Other meta data
	object_temporal_coverage	text		NOT NULL,
	object_transcriber			text		NOT NULL,
	
#	KJV object type metadata
	objecttype_id				int(11)		UNSIGNED NULL,

#	Dublin core referenced in other tables
	contributor_id				int(11)		UNSIGNED NULL,

#	Object consents and status	
	object_status				enum( 'review', 'approved', 'rejected', 'unknown' )	NOT NULL default 'unknown',
	object_contributor_consent	enum( 'yes', 'no', 'unknown') 						NOT NULL default 'unknown',
	object_contributor_posting	enum( 'yes', 'no', 'anonymously', 'unknown') 		NOT NULL default 'unknown',
	
	object_added				timestamp	NOT NULL default '0000-00-00 00:00:00',
	object_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

#	User id, if any of the user who submitted the object
#	Object transcriber
	user_id						int(11)		UNSIGNED NULL,
	
	PRIMARY KEY  (object_id)

) ENGINE=innodb DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS objectsTotal;
CREATE TABLE objectsTotal (
	total	int(11)		NOT NULL default 0
);
INSERT INTO objectsTotal (total) VALUES (0);

#DROP TRIGGER objects_plus;
CREATE TRIGGER objects_plus AFTER INSERT ON objects
	FOR EACH ROW
		UPDATE objectsTotal SET total = total + 1;

#DROP TRIGGER objects_minus;
CREATE TRIGGER objects_minus AFTER DELETE ON objects
	FOR EACH ROW
		UPDATE objectsTotal SET total = total - 1;

-- --------------------------------------------------------

DROP TABLE IF EXISTS objectTypes;
CREATE TABLE objectTypes (
	objectType_id						int(11) UNSIGNED NOT NULL auto_increment,
  	objectType_name						tinytext NOT NULL,
	PRIMARY KEY  (objectType_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dublin core object types
--
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Text' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Still Image' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Sound' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Moving Image' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Interactive Resource' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Mixed Type' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Physical Object' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Service' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Software' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Collection' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Event' );
INSERT INTO objectTypes ( objectType_name ) VALUES ( 'Dataset' );

-- --------------------------------------------------------

--
-- KJV Object Types
--

DROP TABLE IF EXISTS KJVObjectTypes;
CREATE TABLE KJVObjectTypes (
	KJVObjectType_id						int(11)		UNSIGNED NOT NULL auto_increment,
	KJVObjectType_parent_id					int(11) 	UNSIGNED NULL,
	KJVObjectType_name						tinytext	NOT NULL,
	KJVObjectType_description				text		NOT NULL,
	KJVObjectType_active					tinyint(1)	UNSIGNED NOT NULL default '1', -- This may be switched to 0

	PRIMARY KEY  (KJVObjectType_id)

)   ENGINE = innodb DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- kjvObjectTypes_metaFields
-- 

DROP TABLE IF EXISTS KJVObjectTypes_metaFields;
CREATE TABLE KJVObjectTypes_metaFields (
  KJVObjectType_id				int(11) UNSIGNED NOT NULL,
  metaField_id					int(11) UNSIGNED NOT NULL
) ENGINE = innodb DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS metaFields;
CREATE TABLE metaFields (
	metaField_id				int(11)			UNSIGNED NOT NULL auto_increment,
	metaField_name				varchar(100)	NOT NULL,
	metaField_description		text			NOT NULL,

	PRIMARY KEY (metaField_id)

) ENGINE = innodb DEFAULT CHARSET=latin1;

--
-- May need to keep this table MyISAM for full-text searches
--

DROP TABLE IF EXISTS metaText;
CREATE TABLE metaText (
	metaText_id					int(11)			UNSIGNED NOT NULL	auto_increment,
	metaField_id				int(11)			UNSIGNED NOT NULL,
	object_id					int(11)			UNSIGNED NOT NULL,
	metaText_text				text			NOT NULL,

	PRIMARY KEY (metaText_id)

) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table collections
-- 
DROP TABLE IF EXISTS collections;
CREATE TABLE collections (
  collection_id				int(11)		UNSIGNED NOT NULL auto_increment,
  collection_parent_id		int(11)		UNSIGNED NULL,
  collection_name			tinytext	NOT NULL,
  collection_description	text		NOT NULL,
  collection_active			tinyint(1)	UNSIGNED NOT NULL default '1', -- This may be switched to 0

  PRIMARY KEY  (collection_id)

) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS objects_collections;
CREATE TABLE objects_collections (
	object_id				int(11)		UNSIGNED NOT NULL,
	collection_id			int(11)		UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Object Contributors
--
DROP TABLE IF EXISTS contributors;
CREATE TABLE contributors (
	contributor_id				int(11)								UNSIGNED NOT NULL auto_increment,
	contributor_first_name		varchar(100)						NOT NULL,
	contributor_middle_name		varchar(100)						NOT NULL,
	contributor_last_name		varchar(100)						NOT NULL,
	contributor_email			varchar(100)						NOT NULL,
	contributor_phone			varchar(40)							NOT NULL,
	contributor_birth_year		int(4)								UNSIGNED NOT NULL,
	contributor_gender			enum( 'male', 'female', 'unknown' )	default 'unknown',
	contributor_race			enum( 'Asian/Pacific', 'Islander', 'Black', 'Hispanic', 'Native American / Indian', 'White', 'Other', 'unknown' )	default 'unknown',
	contributor_race_other		tinytext							NOT NULL,
	contributor_contact_consent	enum( 'yes', 'no', 'unknown' )		default 'unknown',

	contributor_full_name				text	NOT NULL,
	contributor_fax						text	NOT NULL,
	contributor_address					text	NOT NULL,
	contributor_city					text	NOT NULL,
	contributor_state					text	NOT NULL,
	contributor_zipcode					text	NOT NULL,
	contributor_occupation				text	NOT NULL,
	contributor_jewish					text	NOT NULL,
	contributor_identification			text	NOT NULL,
	contributor_location_during			text	NOT NULL,
	contributor_location_evacuation		text	NOT NULL,
	contributor_location_current		text	NOT NULL,
	contributor_location_between		text	NOT NULL,
	contributor_return					text	NOT NULL,
	contributor_family_members			text	NOT NULL,
	contributor_former_resident			text	NOT NULL,
	contributor_community_evacuees		text	NOT NULL,
	contributor_participate				text	NOT NULL,
	contributor_other_relationship		text	NOT NULL,
	contributor_residence				text	NOT NULL,
	contributor_location_participate	text	NOT NULL,
	
	PRIMARY KEY (contributor_id)

) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Files
--

DROP TABLE IF EXISTS files;
CREATE TABLE files (
	file_id						int(11)		UNSIGNED NOT NULL auto_increment,

#	Dublin core
	file_title					tinytext	NOT NULL,

#	Represented by file_producer
	file_creator				text		NOT NULL,
	file_publisher				text		NOT NULL,
	file_format					text		NOT NULL,
	file_identifier				text		NOT NULL,
	file_source					text		NOT NULL,
	file_language				text		NOT NULL,
	file_relation				text		NOT NULL,
	file_coverage				text		NOT NULL,
	file_rights					text		NOT NULL,
	file_subject				text		NOT NULL,
	file_description			text		NOT NULL,
	file_date					text		NOT NULL,

#	Dublin core referenced in other tables
	object_id					int(11)		UNSIGNED NULL,
	contributor_id				int(11)		UNSIGNED NULL,
	objectType_id				int(11)		UNSIGNED NULL,

#	File preservation and digitization metadata
	file_transcriber			text	NOT NULL,
	file_producer				text	NOT NULL,
	file_render_device			text	NOT NULL,
	file_render_details			text	NOT NULL,
	file_capture_date			text	NOT NULL,
	file_capture_device			text	NOT NULL,
	file_change_history			text	NOT NULL,
	file_watermark				text	NOT NULL,
	file_authentication			text	NOT NULL,
	file_encryption				text	NOT NULL,
	file_compression			text	NOT NULL,
	file_post_processing		text	NOT NULL,
	

#	Physical file related data
	file_archive_filename		tinytext	NOT NULL,
	file_original_filename		tinytext	NOT NULL,
	file_thumbnail_name			tinytext	NOT NULL,
	file_size					int(11)		UNSIGNED NOT NULL default '0',	
	file_mime_browser			tinytext	NOT NULL,
	file_mime_php				tinytext	NOT NULL,
	file_mime_os				tinytext	NOT NULL,
	file_type_os				tinytext	NOT NULL,
	
	file_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	file_added					timestamp	NOT NULL default '0000-00-00 00:00:00',

  PRIMARY KEY  (file_id)

) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Users
--

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  user_id				int(11)		NOT NULL auto_increment,
  user_username			varchar(30) NOT NULL,
  user_password			varchar(40) NOT NULL,
  user_first_name		tinytext	NOT NULL,
  user_last_name		tinytext	NOT NULL,
  user_email			tinytext	NOT NULL,
  user_institution		text		NOT NULL,
  user_permission_id	int(11)		UNSIGNED NOT NULL default '100',
  user_active			int(1)		UNSIGNED NOT NULL default '1',

  PRIMARY KEY  (user_id)

) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Geolocation
--

DROP TABLE IF EXISTS geolocation;
CREATE TABLE geolocation(
	id				int(11)			NOT NULL auto_increment,
	object_id		int(11)			NOT NULL,
	latitude		float(5,5)		NOT NULL,
	longitude		float(5,5)		NOT NULL,
	zoomLevel		varchar(40)		NOT NULL,
	mapType			varchar(100)	NOT NULL,

	PRIMARY KEY(id)

) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Tags
--

DROP TABLE IF EXISTS tags;
CREATE TABLE tags(
	id				int(11)			UNSIGNED NOT NULL auto_increment,
	tag				varchar(255)	NOT NULL,

	PRIMARY KEY(id)

) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS objects_tags;
CREATE TABLE objects_tags(
	object_id		int(11)			UNSIGNED NOT NULL,
	tags_id			int(11)			UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


-- --------------------------------------------------------
-- TEST DATA
-- --------------------------------------------------------

INSERT INTO users ( user_username, user_password, user_first_name, user_last_name, user_email, user_institution, user_permission_id, user_active )
			VALUES ( 'super', SHA1('super'), 'Nate', 'Agrin', 'n8agrin@yahoo.com', 'CHNM', '1', '1' );
			
INSERT INTO users ( user_username, user_password, user_first_name, user_last_name, user_email, user_institution, user_permission_id, user_active )
			VALUES ( 'admin', SHA1('admin'), 'Nate', 'Agrin', 'n8agrin@yahoo.com', 'CHNM', '10', '1' );
			
INSERT INTO users ( user_username, user_password, user_first_name, user_last_name, user_email, user_institution, user_permission_id, user_active )
			VALUES ( 're', SHA1('re'), 'Nate', 'Agrin', 'n8agrin@yahoo.com', 'CHNM', '20', '1' );
			
INSERT INTO users ( user_username, user_password, user_first_name, user_last_name, user_email, user_institution, user_permission_id, user_active )
			VALUES ( 'public', SHA1('public'), 'Nate', 'Agrin', 'n8agrin@yahoo.com', 'CHNM', '50', '1' );
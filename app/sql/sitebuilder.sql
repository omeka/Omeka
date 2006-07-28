--
-- Working structure 5/10/06
-- Nate Agrin, et al.

-- 
-- Table structure for table 'objects'
-- 

DROP TABLE IF EXISTS objects;
CREATE TABLE objects (
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
	
#	Non standard meta data
	object_temporal_coverage	text		NOT NULL,
	object_transcriber			text		NOT NULL,

#	Dublin core referenced in other tables
	contributor_id				int(11)		UNSIGNED NULL,
	objectType_id				int(11)		UNSIGNED NULL,
	
#	Object category (**family**) metadata
	category_id					int(11)		UNSIGNED NULL,

#	Object consents and status	
	object_status				enum( 'review', 'approved', 'rejected', 'unknown' )	NOT NULL default 'unknown',
	object_contributor_consent	enum( 'yes', 'no', 'unknown') 						NOT NULL default 'unknown',
	object_contributor_posting	enum( 'yes', 'no', 'anonymously', 'unknown') 		NOT NULL default 'unknown',
	
	object_added				timestamp	NOT NULL default '0000-00-00 00:00:00',
	object_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

#	permission_id				tinyint		UNSIGNED NOT NULL default '0',
	user_id						int(11)		UNSIGNED NULL,
	
	PRIMARY KEY  (object_id)
) ENGINE=innodb DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS objectsCount;
CREATE TABLE objectsCount (
	count	int(11)		NOT NULL default 0
);
INSERT INTO objectsCount (count) VALUES (0);

#DROP TRIGGER objects_count;
CREATE TRIGGER objects_count AFTER INSERT ON objects
	FOR EACH ROW
		UPDATE objectsCount SET count = count + 1;

CREATE TRIGGER objects_remove AFTER DELETE ON objects
	FOR EACH ROW
		UPDATE objectsCount SET count = count - 1;
	
-- --------------------------------------------------------

-- 
-- Table structure for table 'objectTypes'
-- 

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
-- Object Categories
--

DROP TABLE IF EXISTS categories;
CREATE TABLE categories (
	category_id							int(11)		UNSIGNED NOT NULL auto_increment,
	category_parent_id					int(11) 	UNSIGNED NULL,
	category_name						tinytext	NOT NULL,
	category_description				text		NOT NULL,
	category_active						tinyint(1)	UNSIGNED NOT NULL default '1', -- This may be switched to 0
	PRIMARY KEY  (category_id)
)   ENGINE = innodb DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Object Categories
--
/*
	Future versions of sitebuilder may allow for
	objects to be in multiple categories or
	to have child categories inside parent categories

	DROP TABLE IF EXISTS objects_categories;
	CREATE TABLE objects_categories (
	  object_id				int(11) UNSIGNED NOT NULL,
	  category_id			int(11) UNSIGNED NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;

*/

-- --------------------------------------------------------

-- 
-- Table structure for table 'objectCategories_metaFields'
-- 

DROP TABLE IF EXISTS categories_metaFields;
CREATE TABLE categories_metaFields (
  category_id					int(11) UNSIGNED NOT NULL,
  metaField_id					int(11) UNSIGNED NOT NULL
) ENGINE = innodb DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS metaFields;
CREATE TABLE metaFields (
	metaField_id				int(11)			UNSIGNED NOT NULL auto_increment,
	metaField_name				varchar(100)	NOT NULL,
#	metaField_type				varchar(100)	NOT NULL,
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
-- Table structure for table objectCollections
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
	
	-- The following are non-standard --
	-- this functionality should be a seperate table in future versions --
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
-- Object Files
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
	file_size					int(11)		UNSIGNED NOT NULL default '0',	
	file_mime_browser			tinytext	NOT NULL,
	file_mime_php				tinytext	NOT NULL,
	file_mime_os				tinytext	NOT NULL,
	file_type_os				tinytext	NOT NULL,
#	file_md5sum					varchar(40)	NOT NULL,  // appears broken for unknown reasons
	
	file_modified				timestamp	NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	file_added					timestamp	NOT NULL default '0000-00-00 00:00:00',
#	file_active					int(1)		UNSIGNED NOT NULL default '1',
  PRIMARY KEY  (file_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Permissions
--

DROP TABLE IF EXISTS permissions;
CREATE TABLE permissions (
  permission_id				int(11)		NOT NULL,
  permission_name			tinytext	NOT NULL,
  permission_description	text		NOT NULL,
  PRIMARY KEY  (permission_id)
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
);

--
-- Tags
--

DROP TABLE IF EXISTS tags;
CREATE TABLE tags(
	id				int(11)			UNSIGNED NOT NULL auto_increment,
	tag				varchar(255)	NOT NULL,
	PRIMARY KEY(id)
);

DROP TABLE IF EXISTS objects_tags;
CREATE TABLE objects_tags(
	object_id		int(11)			UNSIGNED NOT NULL,
	tags_id			int(11)			UNSIGNED NOT NULL
);


-- --------------------------------------------------------
-- TEST DATA
-- --------------------------------------------------------

INSERT INTO objects (objectType_id, user_id, object_title, contributor_id, object_subject, object_description, object_date)
	VALUES ( '1', '1', 'Test object', '1', 'Testing', 'Tester', NOW());
	
INSERT INTO categories ( category_id, category_name, category_description, category_active ) VALUES ( '1', 'Image', 'still images', '1' );

INSERT INTO metaFields ( metaField_name, metaField_description ) VALUES ( 'resolution', 'the resolution of the camera used' );
INSERT INTO metaFields ( metaField_name, metaField_description ) VALUES ( 'camera', 'the model or brand of camera used' );
INSERT INTO metaFields ( metaField_name, metaField_description ) VALUES ( 'location', 'the location of the image' );

INSERT INTO categories_metaFields ( category_id, metaField_id ) VALUES ( '1', '1' );
INSERT INTO categories_metaFields ( category_id, metaField_id ) VALUES ( '1', '2' );
INSERT INTO categories_metaFields ( category_id, metaField_id ) VALUES ( '1', '3' );

INSERT INTO metaText ( metaField_id, object_id, metaText_text ) VALUES ( '1', '1', '1080 pixels' );
INSERT INTO metaText ( metaField_id, object_id, metaText_text ) VALUES ( '2', '1', 'Canon 20d' );
INSERT INTO metaText ( metaField_id, object_id, metaText_text ) VALUES ( '3', '1', 'Mt Vernon' );
INSERT INTO users ( user_username, user_password, user_first_name, user_last_name, user_email, user_institution, user_permission_id, user_active )
			VALUES ( 'super', SHA1('super'), 'Nate', 'Agrin', 'n8agrin@yahoo.com', 'CHNM', '1', '1' );
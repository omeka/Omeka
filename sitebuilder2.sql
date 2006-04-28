-- Database: 'sitebuilder2'
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table 'objects'
-- 

DROP TABLE IF EXISTS objects;
CREATE TABLE objects (
  objectID				int(11) NOT NULL auto_increment,
  objectTypeID			int(11) NOT NULL default '0',
  objectPermissionID	int(11) NOT NULL default '0',
  objectUserID			int(11) NOT NULL default '0',
  objectTitle			text,
  objectCreator			text,
  objectSubject			text,
  objectDescription		text,
  objectDate			text,
  objectData			text,
  objectModified		timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  objectAdded			timestamp NOT NULL default '0000-00-00 00:00:00',
  objectActive			int(1) NOT NULL default '0',
  PRIMARY KEY  (objectID),
  FULLTEXT KEY objectTitle (objectTitle,objectDescription,objectData,objectCreator,objectSubject,objectDate)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Table structure for table 'objectTypes'
-- 

DROP TABLE IF EXISTS objectTypes;
CREATE TABLE objectTypes (
  objectTypeID					int(11) NOT NULL auto_increment,
  objectTypeName				tinytext NOT NULL,
  objectTypeDescription			text,
  objectTitleDescription		text,
  objectDescriptionDescription	text,
  objectCreatorDescription		text,
  objectSubjectDescription		text,
  objectDateDescription			text,
  objectTypeActive				int(1) NOT NULL default '0',
  PRIMARY KEY  (objectTypeID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table 'objectTypes_metaFields'
-- 

DROP TABLE IF EXISTS objectTypes_metaFields;
CREATE TABLE objectTypes_metaFields (
  objectType_id					int(11) NOT NULL,
  metaField_id					int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

DROP TABLE IF EXISTS metaFields;
CREATE TABLE metaFields (
	metaField_id				int(11)			NOT NULL	auto_increment,
	metaField_name				varchar(100)	NOT NULL,
	metaField_type				varchar(100)	NOT NULL,
	PRIMARY KEY (metaField_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS metaText;
CREATE TABLE metaText (
	metaText_id					int(11)			NOT NULL	auto_increment,
	metaText_metaField_id		int(11)			NOT NULL,
	metaText_object_id			int(11)			NOT NULL,
	metaText_text				text			NULL,
	PRIMARY KEY (metaText_id)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- TEST DATA
-- --------------------------------------------------------

INSERT INTO objects (objectTypeID, objectPermissionID, objectUserID, objectTitle, objectCreator, objectSubject, objectDescription, objectDate, objectData, objectActive)
	VALUES ( '1', '1', '1', 'Test object', 'Nate', 'Testing', 'Tester', NOW(), 'foo', '1' );
	
INSERT INTO objectTypes (objectTypeID, objectTypeName, objectTypeActive ) VALUES ( '1', 'Image', '1' );

INSERT INTO metaFields ( metaField_name, metaField_type ) VALUES ( 'resolution', 'varchar' );
INSERT INTO metaFields ( metaField_name, metaField_type ) VALUES ( 'camera', 'varchar' );
INSERT INTO metaFields ( metaField_name, metaField_type ) VALUES ( 'location', 'varchar' );

INSERT INTO objectTypes_metaFields ( objectType_id, metaField_id ) VALUES ( '1', '1' );
INSERT INTO objectTypes_metaFields ( objectType_id, metaField_id ) VALUES ( '1', '2' );
INSERT INTO objectTypes_metaFields ( objectType_id, metaField_id ) VALUES ( '1', '3' );

INSERT INTO metaText ( metaText_metaField_id, metaText_object_id, metaText_text ) VALUES ( '1', '1', '1080 pixels' );
INSERT INTO metaText ( metaText_metaField_id, metaText_object_id, metaText_text ) VALUES ( '2', '1', 'Canon 20d' );
INSERT INTO metaText ( metaText_metaField_id, metaText_object_id, metaText_text ) VALUES ( '3', '1', 'Mt Vernon' );
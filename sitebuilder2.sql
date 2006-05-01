-- Database: 'sitebuilder2'
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table 'objects'
-- 

DROP TABLE IF EXISTS objects;
CREATE TABLE objects (
  object_id					int(11) NOT NULL auto_increment,
  objectType_id				int(11) NOT NULL default '0',
  object_permission_id		int(11) NOT NULL default '0',
  object_user_id			int(11) NOT NULL default '0',
  object_title				text,
  object_creator			text,
  object_subject			text,
  object_description		text,
  object_date				text,
  object_data				text,
  object_modified			timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  object_added				timestamp NOT NULL default '0000-00-00 00:00:00',
  object_active				tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (object_id),
  FULLTEXT KEY object_title (object_title, object_description, object_data, object_creator, object_subject, object_date)
) ENGINE=INNODB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `objectCategories`;
CREATE TABLE `objectCategories` (
  `objectCategoryID` int(11) NOT NULL auto_increment,
  `objectCategoryParentID` int(11) default '0',
  `objectCategoryName` tinytext NOT NULL,
  `objectCategoryDescription` text,
  `objectCategoryOrder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`objectCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

DROP TABLE IF EXISTS `objectFiles`;
CREATE TABLE `objectFiles` (
  `objectFileID` int(11) NOT NULL auto_increment,
  `objectFileObjectID` int(11) NOT NULL default '0',
  `objectFileName` text NOT NULL,
  `objectFileOriginalName` text NOT NULL,
  `objectFileDescription` text,
  `objectFileType` tinytext NOT NULL,
  `objectFileSize` int(11) NOT NULL default '0',
  `objectFileModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `objectFileAdded` timestamp NOT NULL default '0000-00-00 00:00:00',
  `objectFileActive` int(1) NOT NULL default '0',
  PRIMARY KEY  (`objectFileID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

DROP TABLE IF EXISTS `objectNotes`;
CREATE TABLE `objectNotes` (
  `objectNoteID` int(11) NOT NULL auto_increment,
  `objectNoteObjectID` int(11) NOT NULL default '0',
  `objectNoteUserID` int(11) NOT NULL default '0',
  `objectNotePermissionID` int(11) NOT NULL default '0',
  `objectNoteText` text NOT NULL,
  `objectNoteModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `objectNoteAdded` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`objectNoteID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `objects_objectCategories`;
CREATE TABLE `objects_objectCategories` (
  `objectID` int(11) NOT NULL default '0',
  `objectCategoryID` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `permissionID` int(11) NOT NULL default '0',
  `permissionName` tinytext NOT NULL,
  `permissionDescription` text,
  PRIMARY KEY  (`permissionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userID` int(11) NOT NULL auto_increment,
  `userUsername` varchar(30) NOT NULL default '',
  `userPassword` varchar(32) NOT NULL default '',
  `userFName` tinytext,
  `userLName` tinytext,
  `userEmail` tinytext,
  `userInstitution` text,
  `userPermissionID` int(11) NOT NULL default '0',
  `userActive` int(1) NOT NULL default '0',
  PRIMARY KEY  (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;


-- 
-- Table structure for table 'objectTypes'
-- 

DROP TABLE IF EXISTS objectTypes;
CREATE TABLE objectTypes (
  objectType_id							int(11) NOT NULL auto_increment,
  objectType_name						tinytext NOT NULL,
  objectType_description				text,
  objectType_title_description			text,
  objectType_description_description	text,
  objectType_creator_description		text,
  objectType_subject_description		text,
  objectType_date_description			text,
  objectType_active						tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (objectType_id)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

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
	metaField_id				int(11)			NOT NULL,
	object_id					int(11)			NOT NULL,
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
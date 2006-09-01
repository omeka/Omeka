-- phpMyAdmin SQL Dump
-- version 2.8.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 27, 2006 at 11:12 AM
-- Server version: 5.0.16
-- PHP Version: 5.1.2
-- 
-- Database: `adam`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `objectCategories`
-- 

DROP TABLE IF EXISTS `objectCategories`;
CREATE TABLE `objectCategories` (
  `objectCategoryID` int(11) NOT NULL auto_increment,
  `objectCategoryParentID` int(11) default '0',
  `objectCategoryName` tinytext NOT NULL,
  `objectCategoryDescription` text,
  `objectCategoryOrder` int(11) NOT NULL default '0',
  PRIMARY KEY  (`objectCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `objectCategories`
-- 

INSERT INTO `objectCategories` (`objectCategoryID`, `objectCategoryParentID`, `objectCategoryName`, `objectCategoryDescription`, `objectCategoryOrder`) VALUES (1, 0, 'ROOT CATEGORY', NULL, 1),
(2, 1, 'Collections', NULL, 1),
(3, 1, 'Collection Objects', NULL, 2),
(4, 1, 'Collection Contributors', NULL, 3),
(5, 1, 'Large Images', NULL, 4);

-- --------------------------------------------------------

-- 
-- Table structure for table `objectFiles`
-- 

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

-- 
-- Dumping data for table `objectFiles`
-- 

INSERT INTO `objectFiles` (`objectFileID`, `objectFileObjectID`, `objectFileName`, `objectFileOriginalName`, `objectFileDescription`, `objectFileType`, `objectFileSize`, `objectFileModified`, `objectFileAdded`, `objectFileActive`) VALUES (1, 1, 'voicemail_fca1e83ba4.doc', 'voicemail.doc', '', 'application/msword', 20992, '2006-03-02 16:31:53', '2006-03-02 16:31:53', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `objectNotes`
-- 

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

-- 
-- Dumping data for table `objectNotes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `objectType_images`
-- 

DROP TABLE IF EXISTS `objectType_images`;
CREATE TABLE `objectType_images` (
  `imageID` int(11) NOT NULL auto_increment,
  `objectID` int(11) NOT NULL,
  `objectTypeID` int(11) NOT NULL,
  `imageExtnd1` text,
  `imageExtend2` text,
  PRIMARY KEY  (`imageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `objectType_images`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `objectTypes`
-- 

DROP TABLE IF EXISTS `objectTypes`;
CREATE TABLE `objectTypes` (
  `objectTypeID` int(11) NOT NULL auto_increment,
  `objectTypeName` tinytext NOT NULL,
  `objectTypeTableName` tinytext NOT NULL,
  `objectTypeDescription` text,
  `objectTypeMetadataDump` text,
  `objectTitleDescription` text,
  `objectDescriptionDescription` text,
  `objectCreatorDescription` text,
  `objectSubjectDescription` text,
  `objectDateDescription` text,
  `objectTypeActive` int(1) NOT NULL default '0',
  PRIMARY KEY  (`objectTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `objectTypes`
-- 

INSERT INTO `objectTypes` (`objectTypeID`, `objectTypeName`, `objectTypeTableName`, `objectTypeDescription`, `objectTypeMetadataDump`, `objectTitleDescription`, `objectDescriptionDescription`, `objectCreatorDescription`, `objectSubjectDescription`, `objectDateDescription`, `objectTypeActive`) VALUES (1, 'image', 'objectType_images', 'digital images', 'a:2:{i:0;a:3:{i:0;s:7:"extnd 1";i:1;s:3:"foo";i:2;s:9:"textInput";}i:1;a:3:{i:0;s:8:"extend 2";i:1;s:3:"bar";i:2;s:8:"textArea";}}', 'title', 'descriptoion', 'author', 'keyworkd', 'date', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `objects`
-- 

DROP TABLE IF EXISTS `objects`;
CREATE TABLE `objects` (
  `objectID` int(11) NOT NULL auto_increment,
  `objectTypeID` int(11) NOT NULL default '0',
  `objectPermissionID` int(11) NOT NULL default '0',
  `objectUserID` int(11) NOT NULL default '0',
  `objectTitle` text,
  `objectCreator` text,
  `objectSubject` text,
  `objectDescription` text,
  `objectDate` text,
  `objectData` text,
  `objectModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `objectAdded` timestamp NOT NULL default '0000-00-00 00:00:00',
  `objectActive` int(1) NOT NULL default '0',
  PRIMARY KEY  (`objectID`),
  FULLTEXT KEY `objectTitle` (`objectTitle`,`objectDescription`,`objectData`,`objectCreator`,`objectSubject`,`objectDate`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `objects`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `objects_objectCategories`
-- 

DROP TABLE IF EXISTS `objects_objectCategories`;
CREATE TABLE `objects_objectCategories` (
  `objectID` int(11) NOT NULL default '0',
  `objectCategoryID` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `objects_objectCategories`
-- 

INSERT INTO `objects_objectCategories` (`objectID`, `objectCategoryID`) VALUES (1, 5),
(2, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `permissions`
-- 

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `permissionID` int(11) NOT NULL default '0',
  `permissionName` tinytext NOT NULL,
  `permissionDescription` text,
  PRIMARY KEY  (`permissionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `permissions`
-- 

INSERT INTO `permissions` (`permissionID`, `permissionName`, `permissionDescription`) VALUES (1, 'Super User', NULL),
(10, 'Administrator', NULL),
(20, 'Researcher', NULL),
(30, 'Public', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

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
-- Dumping data for table `users`
-- 

INSERT INTO `users` (`userID`, `userUsername`, `userPassword`, `userFName`, `userLName`, `userEmail`, `userInstitution`, `userPermissionID`, `userActive`) VALUES (1, 'super', '1b3231655cebb7a1f783eddf27d254ca', 'James', 'Safley', 'jsafley@gmu.edu', 'Center for History and New Media', 1, 1),
(2, 'admin', '1a1dc91c907325c69271ddf0c944bc72', '', '', '', NULL, 10, 1),
(3, 'researcher', '1a1dc91c907325c69271ddf0c944bc72', NULL, NULL, NULL, NULL, 20, 1),
(4, 'public', '5f4dcc3b5aa765d61d8327deb882cf99', NULL, NULL, NULL, NULL, 30, 1),
(5, 'chnm', 'fbed2c1a2ec84c3deeaa9dd7c8cd27d2', NULL, NULL, NULL, NULL, 10, 1);

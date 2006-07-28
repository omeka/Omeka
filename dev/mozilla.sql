-- phpMyAdmin SQL Dump
-- version 2.7.0-pl2
-- http://www.phpmyadmin.net
-- 
-- Host: mysql.localdomain
-- Generation Time: May 10, 2006 at 04:42 PM
-- Server version: 5.0.18
-- PHP Version: 5.1.2
-- 
-- Database: `mozilla`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

CREATE TABLE `categories` (
  `categoryID` int(11) NOT NULL auto_increment,
  `categoryName` tinytext NOT NULL,
  PRIMARY KEY  (`categoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `contributors`
-- 

CREATE TABLE `contributors` (
  `contributorID` int(11) NOT NULL auto_increment,
  `contributorName` tinytext,
  `contributorEmail` tinytext,
  `contributorZipCode` tinytext,
  `contributorBirthYear` tinytext,
  `contributorGender` enum('male','female','unknown') default NULL,
  `contributorRace` enum('Asian/Pacific Islander','Black','Hispanic','Native/American Indian','White','Other','unknown') default NULL,
  `contributorRaceOther` tinytext,
  `contributorOccupation` tinytext,
  `contributorType` enum('fan','developer','unknown') default NULL,
  `contributorProducts` set('Firefox','Thunderbird','Camino','Mozilla','Calendar','Bugzilla','Seamonkey','Other','unknown') default NULL,
  `contributorProductsOther` tinytext,
  `contributorContact` enum('yes','no','unknown') default NULL,
  `contributorIP` tinytext,
  PRIMARY KEY  (`contributorID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

CREATE TABLE `files` (
  `fileID` int(11) NOT NULL auto_increment,
  `objectID` int(11) NOT NULL,
  `fileNameOriginal` tinytext NOT NULL,
  `fileName` tinytext NOT NULL,
  `fileSize` int(11) NOT NULL,
  `fileMimeBrowser` tinytext,
  `fileMimePhp` tinytext,
  `fileMimeOS` tinytext,
  `fileTypeOS` tinytext,
  PRIMARY KEY  (`fileID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `geolocations`
-- 

CREATE TABLE `geolocations` (
  `geolocationID` int(11) NOT NULL auto_increment,
  `objectID` int(11) NOT NULL,
  `geolocationType` tinytext,
  `latitude` tinytext NOT NULL,
  `longitude` tinytext NOT NULL,
  `zoomLevel` tinyint(4) default NULL,
  `mapVersion` tinytext,
  PRIMARY KEY  (`geolocationID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `objects`
-- 

CREATE TABLE `objects` (
  `objectID` int(11) NOT NULL auto_increment,
  `contributorID` int(11) default NULL,
  `objectTitle` text,
  `objectCreator` text,
  `objectSubject` text,
  `objectDescription` text,
  `objectPublisher` text,
  `objectContributor` text,
  `objectDate` text,
  `objectType` enum('collection','dataset','event','interactive resource','moving image','physical object','service','software','sound','still image','text','mixed type') default NULL,
  `objectFormat` text,
  `objectIdentifier` text,
  `objectSource` text,
  `objectLanguage` text,
  `objectRelation` text,
  `objectCoverage` text,
  `objectRights` text,
  `objectText` text,
  `objectHtml` text,
  `objectStatus` enum('review','approved','rejected') NOT NULL,
  `objectContributorConsent` enum('yes','no','unknown') default NULL,
  `objectPosting` enum('yes','anonymously','no','unknown') default NULL,
  `objectModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `objectAdded` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`objectID`),
  FULLTEXT KEY `objects_index` (`objectTitle`,`objectCreator`,`objectSubject`,`objectDescription`,`objectPublisher`,`objectIdentifier`,`objectSource`,`objectLanguage`,`objectRelation`,`objectCoverage`,`objectRights`,`objectText`,`objectHtml`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `objects_categories`
-- 

CREATE TABLE `objects_categories` (
  `objectID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `zip_codes`
-- 

CREATE TABLE `zip_codes` (
  `zipID` int(11) NOT NULL auto_increment,
  `zip` int(5) unsigned zerofill NOT NULL default '00000',
  `lat` varchar(100) NOT NULL default '',
  `long` varchar(100) NOT NULL default '',
  `town` varchar(50) NOT NULL default '',
  `state` char(2) NOT NULL default '',
  `county` varchar(50) NOT NULL default '',
  `mail-type` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`zipID`),
  KEY `zipcodes` (`zip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=42742 ;

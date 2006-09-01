-- phpMyAdmin SQL Dump
-- version 2.8.1
-- http://www.phpmyadmin.net
-- 
-- Host: mysql.localdomain
-- Generation Time: Aug 04, 2006 at 12:42 PM
-- Server version: 5.0.18
-- PHP Version: 5.1.4
-- 
-- Database: `nagrin_jwa_production`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `categories`
-- 

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `category_name` tinytext NOT NULL,
  `category_description` text NOT NULL,
  `category_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `categories`
-- 

INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (2, 'Blog', 'A resource containing frequent and chronological comments and throughts published on the web.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (3, 'Document', 'A resource containing textual data.  Note that facsimiles or images of texts are still of the genre text.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (4, 'Email', 'A resource containing textual messages and binary attachments sent electronically from one person to another or one person to many people.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (5, 'Interactive Resource', 'A resource which requires interaction from the user to be understood, executed, or experienced.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (6, 'Moving Image', 'A series of visual representations that, when shown in succession, impart an impression of motion.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (7, 'Online File', 'A file and accompanying metadata contributed to JWA through the KJV contribution form.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (8, 'Online Text', 'A story, email, blog, or any other textual data contributed to JWA through the KJV contribution form.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (9, 'Oral History', 'A resource containing historical information obtained in interviews with persons having firsthand knowledge.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (10, 'Sound', 'A resource whose content is primarily intended to be rendered as audio.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (11, 'Still Image', 'A static visual representation. Examples of still images are: paintings, drawings, graphic designs, plans and maps.  Recommended best practice is to assign the type "text" to images of textual materials.', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (12, 'Web Page', 'A resource intended for publication on the World Wide Web using hypertext markup language. Note that the actual HTML page viewed in the exhibit should be replaced here by a PDF (but do copy the HTML in the field, below, for indexing purposes)', 1);
INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `category_active`) VALUES (13, 'Website', 'A resource comprising of a web page or web pages and all related assets ( such as images, sound and video files, etc. ).', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `categories_metafields`
-- 

DROP TABLE IF EXISTS `categories_metafields`;
CREATE TABLE `categories_metafields` (
  `category_id` int(11) unsigned NOT NULL,
  `metafield_id` int(11) unsigned NOT NULL,
  KEY `category_id` (`category_id`),
  KEY `metafield_id` (`metafield_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `categories_metafields`
-- 

INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (2, 2);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (2, 3);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (2, 4);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (3, 5);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (4, 6);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (4, 7);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (4, 8);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (4, 9);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (4, 10);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (4, 11);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (4, 12);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (5, 13);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (6, 14);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (6, 15);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (6, 16);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (6, 17);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (6, 18);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (8, 5);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (9, 19);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (9, 20);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (9, 21);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (9, 22);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (9, 23);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (9, 24);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (9, 25);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (10, 26);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (10, 27);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (10, 24);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (10, 25);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (11, 28);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (11, 25);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (11, 29);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (11, 30);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (12, 31);
INSERT INTO `categories_metafields` (`category_id`, `metafield_id`) VALUES (13, 32);

-- --------------------------------------------------------

-- 
-- Table structure for table `collections`
-- 

DROP TABLE IF EXISTS `collections`;
CREATE TABLE `collections` (
  `collection_id` int(11) unsigned NOT NULL auto_increment,
  `collection_name` tinytext NOT NULL,
  `collection_description` text NOT NULL,
  `collection_active` tinyint(1) unsigned NOT NULL default '0',
  `collection_featured` tinyint(1) unsigned NOT NULL default '0',
  `collection_collector` text NOT NULL,
  PRIMARY KEY  (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `collections`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `contributors`
-- 

DROP TABLE IF EXISTS `contributors`;
CREATE TABLE `contributors` (
  `contributor_id` int(11) unsigned NOT NULL auto_increment,
  `contributor_first_name` varchar(100) NOT NULL,
  `contributor_middle_name` varchar(100) NOT NULL,
  `contributor_last_name` varchar(100) NOT NULL,
  `contributor_email` varchar(100) NOT NULL,
  `contributor_phone` varchar(40) NOT NULL,
  `contributor_birth_year` int(4) default NULL,
  `contributor_gender` enum('male','female','unknown') default 'unknown',
  `contributor_race` enum('Asian / Pacific Islander','Black','Hispanic','Native / American Indian','White','Other','unknown') default 'unknown',
  `contributor_race_other` tinytext NOT NULL,
  `contributor_contact_consent` enum('yes','no','unknown') default 'unknown',
  `contributor_fax` varchar(14) NOT NULL,
  `contributor_address` varchar(100) NOT NULL,
  `contributor_city` varchar(16) NOT NULL,
  `contributor_state` varchar(16) NOT NULL,
  `contributor_zipcode` varchar(10) NOT NULL,
  `contributor_occupation` varchar(255) NOT NULL,
  `contributor_location_participate` text NOT NULL,
  PRIMARY KEY  (`contributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `contributors`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `file_id` int(11) unsigned NOT NULL auto_increment,
  `file_title` varchar(255) NOT NULL,
  `file_publisher` text NOT NULL,
  `file_language` varchar(255) NOT NULL,
  `file_relation` text NOT NULL,
  `file_rights` text NOT NULL,
  `file_description` text NOT NULL,
  `file_date` timestamp NULL default NULL,
  `file_coverage_start` timestamp NULL default NULL,
  `file_coverage_end` timestamp NULL default NULL,
  `object_id` int(11) unsigned default NULL,
  `contributor_id` int(11) unsigned default NULL,
  `file_transcriber` text NOT NULL,
  `file_producer` text NOT NULL,
  `file_render_device` text NOT NULL,
  `file_render_details` text NOT NULL,
  `file_capture_date` timestamp NULL default NULL,
  `file_capture_device` text NOT NULL,
  `file_change_history` text NOT NULL,
  `file_watermark` text NOT NULL,
  `file_authentication` text NOT NULL,
  `file_encryption` text NOT NULL,
  `file_compression` text NOT NULL,
  `file_post_processing` text NOT NULL,
  `file_archive_filename` tinytext NOT NULL,
  `file_original_filename` tinytext NOT NULL,
  `file_thumbnail_name` tinytext NOT NULL,
  `file_size` int(11) unsigned NOT NULL default '0',
  `file_mime_browser` tinytext NOT NULL,
  `file_mime_php` tinytext NOT NULL,
  `file_mime_os` tinytext NOT NULL,
  `file_type_os` tinytext NOT NULL,
  `file_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `file_added` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`file_id`),
  KEY `object_id` (`object_id`),
  KEY `contributor_id` (`contributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `files`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `location`
-- 

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `location_id` int(11) NOT NULL auto_increment,
  `object_id` int(11) unsigned NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` varchar(10) NOT NULL,
  `zoomLevel` varchar(40) NOT NULL,
  `mapType` varchar(100) NOT NULL,
  `cleanAddress` varchar(200) NOT NULL,
  PRIMARY KEY  (`location_id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `location`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `metafields`
-- 

DROP TABLE IF EXISTS `metafields`;
CREATE TABLE `metafields` (
  `metafield_id` int(11) unsigned NOT NULL auto_increment,
  `metafield_name` varchar(100) NOT NULL,
  `metafield_description` text NOT NULL,
  PRIMARY KEY  (`metafield_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `metafields`
-- 

INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (2, 'Body', 'The main body of the blog post.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (3, 'Comments', 'Any comments made in response to the blog post, including the date.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (4, 'Trackbacks', 'A list of all blog postings that have referenced this blog post.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (5, 'Text', 'Any textual data included in the document.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (6, 'Email Body', 'The main body of the email, including all replied and forwarded text and headers.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (7, 'Subject Line', 'The content of the subject line of the email.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (8, 'From', 'The name and email address of the person sending the email.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (9, 'To', 'The name(s) and email address(es) of the person to whom the email was sent.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (10, 'Cc', 'The name(s) and email address(es) of the person to whom the email was carbon copied.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (11, 'Bcc', 'The name(s) and email address(es) of the person to whom the email was blind carbon copied.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (12, 'Number of Attachments', 'The number of attachments to the email.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (13, 'Duration', 'The length of time of the clip, expressed in hours (hr), minutes (min), and/or seconds (sec) as makes best sense.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (14, 'Duration', 'The length of time of the clip, expressed in hours (hr), minutes (min), and/or seconds (sec) as makes best sense.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (15, 'Resolution (in dpi)', 'The resolution of the moving image determined by pixel dimensions, pixels per inch or dots per inch.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (16, 'Bit Depth (in bits)', 'this will be an integer, and will usually be 8 or a multiple of 8.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (17, 'Width (in pixels)', 'The width of the moving image at full size.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (18, 'Height (in pixels)', 'The height of the moving image at full size.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (19, 'Transcription', 'Any written text transcribed from or during the interview.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (20, 'Interviewer', 'The person(s) performing the interview.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (21, 'Interviewee', 'The person(s) being interviewed.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (22, 'Location', 'The location of the interview.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (23, 'Duration', 'The length of time of the interview.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (24, 'Sample Rate', 'The number of samples recorded per second.  Sample rates are measured in Hz or kHz.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (25, 'Bit Depth (in bits)', 'The number of bits used to represent each sample in an audio file, determining the accuracy of the sample.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (26, 'Sound Transcription', 'Any written text transcribed from the sound.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (27, 'Sound Duration', 'The length of time of the sound.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (28, 'Resolution (in dpi)', 'The resolution of the still image.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (29, 'Width (in pixels)', 'The width of the still image at full size.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (30, 'Height (in pixels)', 'The height of the still image at full size.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (31, 'HTML', 'The hypertext markup language used for building the web page.');
INSERT INTO `metafields` (`metafield_id`, `metafield_name`, `metafield_description`) VALUES (32, 'Local URL', 'The URL of the local directory containing all assets of the website. Please talk with the archivist about current preservation practice for websites.');

-- --------------------------------------------------------

-- 
-- Table structure for table `metatext`
-- 

DROP TABLE IF EXISTS `metatext`;
CREATE TABLE `metatext` (
  `metatext_id` int(11) unsigned NOT NULL auto_increment,
  `metafield_id` int(11) unsigned NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `metatext_text` text NOT NULL,
  PRIMARY KEY  (`metatext_id`),
  KEY `metafield_id` (`metafield_id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `metatext`
-- 



-- --------------------------------------------------------

-- 
-- Table structure for table `objects`
-- 

DROP TABLE IF EXISTS `objects`;
CREATE TABLE `objects` (
  `object_id` int(11) unsigned NOT NULL auto_increment,
  `object_title` tinytext NOT NULL,
  `object_publisher` text NOT NULL,
  `object_language` tinytext NOT NULL,
  `object_rights` text NOT NULL,
  `object_description` text NOT NULL,
  `object_date` timestamp NULL default NULL,
  `object_status` enum('review','approved','rejected','notyet','moreinfo') NOT NULL default 'notyet',
  `object_relation` text NOT NULL,
  `category_id` int(11) unsigned default NULL,
  `contributor_id` int(11) unsigned default NULL,
  `creator_id` int(11) unsigned default NULL,
  `creator_other` text NOT NULL,
  `collection_id` int(11) unsigned default NULL,
  `user_id` int(11) unsigned default NULL,
  `object_coverage_start` timestamp NULL default NULL,
  `object_coverage_end` timestamp NULL default NULL,
  `object_contributor_consent` enum('yes','unsure','restrict','no','unknown') NOT NULL default 'unknown',
  `object_contributor_posting` enum('yes','no','anonymously','unknown') NOT NULL default 'unknown',
  `object_added` timestamp NULL default NULL,
  `object_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `object_featured` int(1) NOT NULL default '0',
  PRIMARY KEY  (`object_id`),
  KEY `category_id` (`category_id`),
  KEY `contributor_id` (`contributor_id`),
  KEY `creator_id` (`creator_id`),
  KEY `collection_id` (`collection_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `objects`
-- 



-- --------------------------------------------------------

-- 
-- Table structure for table `objectsTotal`
-- 

DROP TABLE IF EXISTS `objectsTotal`;
CREATE TABLE `objectsTotal` (
  `total` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `objectsTotal`
-- 

INSERT INTO `objectsTotal` (`total`) VALUES (138);

-- --------------------------------------------------------

-- 
-- Table structure for table `objects_favorites`
-- 

DROP TABLE IF EXISTS `objects_favorites`;
CREATE TABLE `objects_favorites` (
  `object_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `fav_added` timestamp NULL default NULL,
  KEY `object_id` (`object_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `objects_favorites`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `objects_tags`
-- 

DROP TABLE IF EXISTS `objects_tags`;
CREATE TABLE `objects_tags` (
  `object_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  KEY `object_id` (`object_id`),
  KEY `tag_id` (`tag_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `objects_tags`
-- 



-- --------------------------------------------------------

-- 
-- Table structure for table `tags`
-- 

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `tag_id` int(11) unsigned NOT NULL auto_increment,
  `tag_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

-- 
-- Dumping data for table `tags`
-- 



-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `user_username` varchar(30) NOT NULL,
  `user_password` varchar(40) NOT NULL,
  `user_first_name` tinytext NOT NULL,
  `user_last_name` tinytext NOT NULL,
  `user_email` tinytext NOT NULL,
  `user_institution` text NOT NULL,
  `user_permission_id` int(11) unsigned NOT NULL default '100',
  `user_active` int(1) unsigned NOT NULL default '0',
  `contributor_id` int(11) unsigned default NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1  ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` (`user_id`, `user_username`, `user_password`, `user_first_name`, `user_last_name`, `user_email`, `user_institution`, `user_permission_id`, `user_active`, `contributor_id`) VALUES (1, 'super', '8451ba8a14d79753d34cb33b51ba46b4b025eb81', 'Super', 'Super', 'super@super.com', 'Sitebuilder Admin', 1, 1, 0);

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `categories_metafields`
-- 
ALTER TABLE `categories_metafields`
  ADD CONSTRAINT `categories_metafields_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `categories_metafields_ibfk_2` FOREIGN KEY (`metafield_id`) REFERENCES `metafields` (`metafield_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `files`
-- 
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `objects` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `files_ibfk_2` FOREIGN KEY (`contributor_id`) REFERENCES `contributors` (`contributor_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 
-- Constraints for table `location`
-- 
ALTER TABLE `location`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `objects` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `metatext`
-- 
ALTER TABLE `metatext`
  ADD CONSTRAINT `metatext_ibfk_1` FOREIGN KEY (`metafield_id`) REFERENCES `metafields` (`metafield_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `metatext_ibfk_2` FOREIGN KEY (`object_id`) REFERENCES `objects` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `objects`
-- 
ALTER TABLE `objects`
  ADD CONSTRAINT `category_key` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `collection_key` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`collection_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `contributor_key` FOREIGN KEY (`contributor_id`) REFERENCES `contributors` (`contributor_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_key` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 
-- Constraints for table `objects_favorites`
-- 
ALTER TABLE `objects_favorites`
  ADD CONSTRAINT `objects_favorites_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `objects` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `objects_favorites_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `objects_tags`
-- 
ALTER TABLE `objects_tags`
  ADD CONSTRAINT `objects_tags_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `objects` (`object_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `objects_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `objects_tags_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

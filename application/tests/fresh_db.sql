-- phpMyAdmin SQL Dump
-- version 2.8.0.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 30, 2007 at 11:03 AM
-- Server version: 5.0.20
-- PHP Version: 5.2.1
-- 
-- Database: `omeka`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `collections`
-- 

DROP TABLE IF EXISTS `collections`;
CREATE TABLE `collections` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci,
  `active` tinyint(1) default NULL,
  `featured` tinyint(1) default NULL,
  `collector` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `files`
-- 

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` text collate utf8_unicode_ci,
  `publisher` text collate utf8_unicode_ci,
  `language` varchar(40) collate utf8_unicode_ci default NULL,
  `relation` text collate utf8_unicode_ci,
  `coverage` text collate utf8_unicode_ci,
  `rights` text collate utf8_unicode_ci,
  `description` text collate utf8_unicode_ci,
  `source` text collate utf8_unicode_ci,
  `subject` text collate utf8_unicode_ci,
  `creator` text collate utf8_unicode_ci,
  `additional_creator` text collate utf8_unicode_ci,
  `date` date default NULL,
  `added` datetime default NULL,
  `modified` datetime default NULL,
  `item_id` bigint(20) default NULL,
  `format` text collate utf8_unicode_ci NOT NULL,
  `transcriber` text collate utf8_unicode_ci,
  `producer` text collate utf8_unicode_ci,
  `render_device` text collate utf8_unicode_ci,
  `render_details` text collate utf8_unicode_ci,
  `capture_date` datetime default NULL,
  `capture_device` text collate utf8_unicode_ci,
  `capture_details` text collate utf8_unicode_ci,
  `change_history` text collate utf8_unicode_ci,
  `watermark` text collate utf8_unicode_ci,
  `authentication` text collate utf8_unicode_ci,
  `encryption` text collate utf8_unicode_ci,
  `compression` text collate utf8_unicode_ci,
  `post_processing` text collate utf8_unicode_ci,
  `archive_filename` text collate utf8_unicode_ci,
  `original_filename` text collate utf8_unicode_ci,
  `size` bigint(20) default NULL,
  `mime_browser` text collate utf8_unicode_ci,
  `mime_os` text collate utf8_unicode_ci,
  `type_os` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `group_id` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `groups_permissions`
-- 

DROP TABLE IF EXISTS `groups_permissions`;
CREATE TABLE `groups_permissions` (
  `id` bigint(20) NOT NULL auto_increment,
  `group_id` bigint(20) NOT NULL,
  `permission_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `items`
-- 

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` text collate utf8_unicode_ci,
  `publisher` text collate utf8_unicode_ci,
  `language` text collate utf8_unicode_ci,
  `relation` text collate utf8_unicode_ci,
  `coverage` text collate utf8_unicode_ci,
  `rights` text collate utf8_unicode_ci,
  `description` text collate utf8_unicode_ci,
  `source` text collate utf8_unicode_ci,
  `subject` text collate utf8_unicode_ci,
  `creator` text collate utf8_unicode_ci,
  `additional_creator` text collate utf8_unicode_ci,
  `date` date default NULL,
  `added` datetime default NULL,
  `modified` datetime default NULL,
  `type_id` bigint(20) default NULL,
  `collection_id` bigint(20) default NULL,
  `user_id` bigint(20) default NULL,
  `featured` tinyint(1) default NULL,
  `public` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `items_favorites`
-- 

DROP TABLE IF EXISTS `items_favorites`;
CREATE TABLE `items_favorites` (
  `id` bigint(20) NOT NULL auto_increment,
  `item_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `added` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `items_fulltext`
-- 

DROP TABLE IF EXISTS `items_fulltext`;
CREATE TABLE `items_fulltext` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL,
  `text` longtext collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `items_tags`
-- 

DROP TABLE IF EXISTS `items_tags`;
CREATE TABLE `items_tags` (
  `id` bigint(20) NOT NULL auto_increment,
  `item_id` bigint(20) NOT NULL,
  `tag_id` bigint(20) NOT NULL,
  `user_id` bigint(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `metafields`
-- 

DROP TABLE IF EXISTS `metafields`;
CREATE TABLE `metafields` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  `description` text collate utf8_unicode_ci,
  `plugin_id` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `metatext`
-- 

DROP TABLE IF EXISTS `metatext`;
CREATE TABLE `metatext` (
  `id` bigint(20) NOT NULL auto_increment,
  `item_id` bigint(20) default NULL,
  `metafield_id` bigint(20) default NULL,
  `text` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `options`
-- 

DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `permissions`
-- 

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `plugins`
-- 

DROP TABLE IF EXISTS `plugins`;
CREATE TABLE `plugins` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  `description` text collate utf8_unicode_ci,
  `author` text collate utf8_unicode_ci,
  `config` text collate utf8_unicode_ci,
  `active` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `tags`
-- 

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `types`
-- 

DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(200) collate utf8_unicode_ci default NULL,
  `description` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `types_metafields`
-- 

DROP TABLE IF EXISTS `types_metafields`;
CREATE TABLE `types_metafields` (
  `id` bigint(20) NOT NULL auto_increment,
  `type_id` bigint(20) NOT NULL,
  `metafield_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) NOT NULL auto_increment,
  `username` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(40) collate utf8_unicode_ci default NULL,
  `first_name` varchar(200) collate utf8_unicode_ci default NULL,
  `last_name` varchar(200) collate utf8_unicode_ci default NULL,
  `email` varchar(200) collate utf8_unicode_ci default NULL,
  `institution` text collate utf8_unicode_ci,
  `active` tinyint(1) default NULL,
  `group_id` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `users_activations`
-- 

DROP TABLE IF EXISTS `users_activations`;
CREATE TABLE `users_activations` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) default NULL,
  `url` varchar(100) collate utf8_unicode_ci default NULL,
  `added` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

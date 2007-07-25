<?php
//Add any remaining tables to the DB if they are not added yet
$this->query("
CREATE TABLE IF NOT EXISTS `sections` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(255)  default NULL,
  `description` text ,
  `exhibit_id` bigint(20) NOT NULL,
  `section_order` bigint(20) NOT NULL,
  `slug` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `section_pages` (
  `id` bigint(20) NOT NULL auto_increment,
  `section_id` bigint(20) NOT NULL,
  `layout` varchar(255)  default NULL,
  `page_order` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `exhibits` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(255)  default NULL,
  `description` text ,
  `credits` text ,
  `featured` tinyint(1) default NULL,
  `theme` varchar(30)  default NULL,
  `slug` varchar(30)  default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `items_section_pages` (
  `id` bigint(20) NOT NULL auto_increment,
  `item_id` bigint(20) default NULL,
  `page_id` bigint(20) NOT NULL,
  `text` text ,
  `entry_order` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");


//Add the slug column to the sections table 
if(!$this->tableHasColumn('sections', 'slug')) {

		$this->query("
	ALTER TABLE `sections` ADD `slug` VARCHAR( 30 ) NOT NULL ;
	ALTER TABLE `sections` ADD INDEX ( `slug` ) ;");	
	
	
	//Now generate slugs for all the slug fields so that it will be working
	
	$titles = $this->query("SELECT id, title FROM sections s");
	
	foreach ($titles as $row) {
		$slug = $row['title'];
		
		$prohibited = array(':', '/', ' ', '.');
		$replace = array_fill(0, count($prohibited), '-');
		$slug = str_replace($prohibited, $replace, strtolower($slug) );
		
		$sql = "UPDATE sections SET slug = ? WHERE id = ?";
		$this->query($sql, array( $slug, $row['id'] ));
	}
	

} 
?>

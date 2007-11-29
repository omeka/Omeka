<?php 
$db = get_db();

$db->exec("ALTER TABLE `sections` CHANGE `section_order` `order` TINYINT UNSIGNED NOT NULL");

$db->exec("ALTER TABLE `section_pages` CHANGE `page_order` `order` TINYINT UNSIGNED NOT NULL");

$db->exec("ALTER TABLE `items_section_pages` CHANGE `entry_order` `order` TINYINT UNSIGNED NOT NULL"); 


//entity_id is a required field in the users table
$db->exec("ALTER TABLE `$db->User` CHANGE `entity_id` `entity_id` BIGINT( 20 ) UNSIGNED NOT NULL ");
?>
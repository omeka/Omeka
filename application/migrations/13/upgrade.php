<?php 
//Optimize the joins on the entities_relations tablee
$db = get_db();

$db->exec("ALTER TABLE `entities_relations` ADD INDEX `relation_type` ( `type` )");
$db->exec("ALTER TABLE `entities_relations` CHANGE `type` `type` ENUM( 'Item', 'Collection', 'Exhibit' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
$db->exec("ALTER TABLE `entities_relations` ADD INDEX `relation` ( `relation_id` ) ");
$db->exec("ALTER TABLE `entities_relations` ADD INDEX `relationship` ( `relationship_id` )");
?>
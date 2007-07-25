<?php 
if(!defined('ITEM_INHERITANCE_ID')) define('ITEM_INHERITANCE_ID', 1);
if(!defined('EXHIBIT_INHERITANCE_ID')) define('EXHIBIT_INHERITANCE_ID', 2);
if(!defined('COLLECTION_INHERITANCE_ID')) define('COLLECTION_INHERITANCE_ID', 3);

//Create the taggings table
if(!$this->hasTable('taggings')) {
	$createTable = 
	"CREATE TABLE `taggings` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `relation_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `inheritance_id` tinyint(2) unsigned NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;
ALTER TABLE `taggings` ADD UNIQUE `tag` ( `relation_id` , `tag_id` , `entity_id` , `inheritance_id` );";

	$this->query($createTable);	
}

//Check for the presence of the items_tags table
if($this->hasTable('items_tags')) {
	
	//Assimilate it into the taggings table

	$this->query(
		"INSERT INTO taggings (relation_id, tag_id, entity_id, inheritance_id) SELECT it.item_id as relation_id, it.tag_id as tag_id, e.id as entity_id, 1 as inheritance_id
		FROM items_tags it
		INNER JOIN users u ON u.id = it.user_id
		INNER JOIN entities e ON e.id = u.entity_id
		");
		
	$this->query("DROP TABLE `items_tags`;");	
}
 
if($this->hasTable('exhibits_tags')) {
	$this->query("
		INSERT INTO taggings (relation_id, tag_id, entity_id, inheritance_id) SELECT et.exhibit_id as relation_id, et.tag_id as tag_id, e.id as entity_id, 2 as inheritance_id
		FROM exhibits_tags et
		INNER JOIN users u ON u.id = et.user_id
		INNER JOIN entities e ON e.id = u.entity_id");
		
	$this->query("DROP TABLE `exhibits_tags");
}

//Change 'inheritance_id' to 'type'
if($this->tableHasColumn('Taggings', 'inheritance_id')) {
	$sql = 
	"ALTER TABLE `entities_relations` CHANGE `inheritance_id` `type` VARCHAR( 50 ) NOT NULL DEFAULT '';
	ALTER TABLE `taggings` CHANGE `inheritance_id` `type` VARCHAR( 50 ) NOT NULL DEFAULT '';
	ALTER TABLE `entities` CHANGE `inheritance_id` `type` VARCHAR( 50 ) NOT NULL DEFAULT '';
	UPDATE `entities_relations` SET type = 'Item' WHERE type = 1;
	UPDATE `entities_relations` SET type = 'Collection' WHERE type = 2;
	UPDATE `entities_relations` SET type = 'Exhibit' WHERE type = 3;
	UPDATE `taggings` SET type = 'Item' WHERE type = 1;
	UPDATE `taggings` SET type = 'Exhibit' WHERE type = 2;
	UPDATE `entities` SET type = 'Anonymous' WHERE type = 1;
	UPDATE `entities` SET type = 'Institution' WHERE  type = 2;
	UPDATE `entities` SET type = 'Person' WHERE type = 3; ";
	$this->query($sql);
}

?>

<?php 
define('ITEM_INHERITANCE_ID', 1);
define('COLLECTION_INHERITANCE_ID', 2);

define('ANONYMOUS_INHERITANCE_ID', 1);
define('INSTITUTION_INHERITANCE_ID', 2);
define('PERSON_INHERITANCE_ID', 3);

//Convert the users table to entities table

/* 1) Make 'entities' table if not exists, add 'entity_id' field to Users table
   2) Loop through Users table, for each entry make a new Entities entry
   3) All of the entities have inheritance_id = 3 (Person)
   4) Drop all unique keys in the users table related to the fields that are now in entities table
   5) Verify that each entry in the users table has a corresponding entry in the entities table
   6) Drop all unused fields from the users table
*/

//Build 'entities' table
if(!$this->hasTable('Entity')) {
	
	$this->query("CREATE TABLE IF NOT EXISTS `entities` (
  `id` bigint(20) NOT NULL auto_increment,
  `first_name` text ,
  `middle_name` text ,
  `last_name` text ,
  `email` text ,
  `institution` text ,
  `parent_id` bigint(20) default NULL,
  `inheritance_id` tinyint(2) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

	INSERT INTO `entities` (first_name, inheritance_id) VALUES ('Anonymous', 1);
");
}

if(!$this->tableHasColumn('User', 'entity_id')) {
	$this->query(
		"ALTER TABLE `users` ADD `entity_id` BIGINT UNSIGNED NULL DEFAULT NULL ;
		ALTER TABLE `users` ADD INDEX ( `entity_id` ) ;");
}

//Only do the following if the users table still has entity table fields
if($this->tableHasColumn('User', 'first_name')) {
	$users = $this->query("SELECT * FROM `users`");

	foreach ($users as $k => $u) {
		
		if(empty($u["entity_id"])) {
		
			$fields = array('first_name','last_name','email','institution');
			
			foreach ($fields as $k => $v) {
				$values[$v] = !empty($u[$v]) ? $u[$v] : null;
			}
			
			//If all the fields are missing, assign it to a new Person
			if(empty($u['first_name']) and empty($u['last_name']) and empty($u['email']) and empty($u['institution']) ) {
				
				//Get the entity_id for the anonymous entity
				
				$entity_id = $this->query("SELECT e.id FROM entities e WHERE e.first_name = 'Anonymous'", array(), true);
				
				//Update the user entry
				$this->query("UPDATE `users` SET entity_id = ? WHERE id = ?", array($entity_id, $u['id']));
				
			}
			//Otherwise insert a new entry into the entities table
			else {
				
				//If the institution is set, insert this as an institution
				if(!empty($values['institution'])) {
					$inheritance_id = INSTITUTION_INHERITANCE_ID;
				}else{
					$inheritance_id = PERSON_INHERITANCE_ID;
				}
				
				$this->query("INSERT INTO `entities` (first_name, last_name, email, institution, inheritance_id) VALUES (?, ?, ?, ?, ?)", 
					array( $values['first_name'], $values['last_name'], $values['email'], $values['institution'], $inheritance_id ) );
				
				//Grab entity ID via query
				$entity_id = $this->query("SELECT LAST_INSERT_ID() as id", array(), true);
				
				//Now update the user row
				$this->query("UPDATE `users` SET entity_id = ? WHERE id = ?", array($entity_id, $u['id']));
			}
		}
	}
	
	//Now remove those fields from the users table
	$this->query("ALTER TABLE `users` DROP `first_name` ,
DROP `last_name` ,
DROP `email` ,
DROP `institution` ;");

}

//Now create the EntitiesRelations table
if(!$this->hasTable('EntitiesRelations')) {
	$this->query("CREATE TABLE IF NOT EXISTS `entities_relations` (
  `id` bigint(20) NOT NULL auto_increment,
  `entity_id` bigint(20) default NULL,
  `relation_id` bigint(20) default NULL,
  `relationship_id` bigint(20) default NULL,
  `inheritance_id` tinyint(2) NOT NULL,
  `time` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

//Next step is to convert all the user_id fields on the Item to EntitiesRelations relationships
if($this->tableHasColumn('Item', 'user_id')) {
	$this->query("
	CREATE TABLE IF NOT EXISTS `entity_relationships` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` text ,
  `description` text ,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
	
	//All the 'added' timestamps are being converted to entries in the entities_relations table
	$this->query("INSERT IGNORE INTO `entity_relationships` (id, name) VALUES (1, 'added')");
	
	//Pull in the relevant data for the conversion
	$items = $this->query(
		"SELECT i.id as relation_id, e.id as entity_id, 1 as relationship_id, i.added as time, 1 as inheritance_id 
		FROM `items` i 
		INNER JOIN `users` u ON u.id = i.user_id 
		INNER JOIN `entities` e ON e.id = u.entity_id");

	$sql = "INSERT INTO `entities_relations` (relation_id, entity_id, relationship_id, time, inheritance_id) VALUES (:relation_id, :entity_id, :relationship_id, :time, :inheritance_id)";

	foreach ($items as $k => $item) {
		$this->query($sql, $item);	
	}
	
	
	//All the 'modified' timestamps are likewise being converted
	$this->query("INSERT IGNORE INTO `entity_relationships` (id, name) VALUES (2, 'modified')");
	$items = $this->query(
		"SELECT i.id as relation_id, e.id as entity_id, 2 as relationship_id, i.modified as time, 1 as inheritance_id
		FROM `items` i
		INNER JOIN `users` u ON u.id = i.user_id
		INNER JOIN `entities` e ON e.id = u.entity_id
		");
	
	foreach ($items as $k => $item) {
		$this->query($sql, $item);	
	}
	
	//Once everything has been converted, drop the 'added','modified' and 'user_id' fields from the items table
	$this->query("ALTER TABLE `items` DROP `added`, DROP `modified`, DROP `user_id`;");
}



//Now we can consolidate the ItemsFavorites table into the entities_relations table
if($this->hasTable('items_favorites')) {
	//Insert the 'favorite' entity relationship
	$this->query("INSERT INTO entity_relationships (id, name) VALUES (3, 'favorite')");
	
	//Now SELECT the info we need for the entities_relations table
	//Again, 1 is the inheritance ID for items
	$ifs = $this->query(
		"SELECT 
				e.id as entity_id, 
				j.item_id as relation_id, 
				3 as relationship_id, 
				1 as inheritance_id, 
				j.added as time 
		FROM items_favorites j
		JOIN users u ON u.id = j.user_id
		JOIN entities e ON e.id = u.entity_id");
		
	$insertSql = "INSERT INTO entities_relations (entity_id, relation_id, relationship_id, inheritance_id, time) VALUES (:entity_id, :relation_id, :relationship_id, :inheritance_id, :time)";	
	
	foreach ($ifs as $if) {
		$this->query($insertSql, $if);
	}
	
	//Now drop the items_favorites table
	$this->query("DROP TABLE `items_favorites`");
}


//Now let's consolidate the collectors into the entities_relations table
if($this->tableHasColumn('Collection', 'collector')) {
	
	//Create the 'collector' relationship in the entity_relationships table
	$this->query("INSERT INTO entity_relationships (id, name) VALUES (4, 'collector')");
	
	//Loop through all the collections and convert the ones that have collectors listed so that they are entities
	$colls = $this->query(
		"SELECT 
			c.id as relation_id, 
			c.collector as entity, ".
			COLLECTION_INHERITANCE_ID." as inheritance_id
		FROM collections c
		");
		
	foreach ($colls as $k => $coll) {
		if(!empty($coll['entity'])) {

			$this->query("INSERT INTO `entities` (first_name, inheritance_id) VALUES (?, ?)", array($coll['entity'], COLLECTION_INHERITANCE_ID));
			
			$entity_id = $this->query("SELECT LAST_INSERT_ID() as id", array(), true);
			
			$sql = "INSERT INTO entities_relations 
						(entity_id, relation_id, inheritance_id, relationship_id, time)
					VALUES (?, ?, ?, ?, NOW())";
					
			$this->query($sql, array($entity_id, $coll['relation_id'], $coll['inheritance_id'], 4));
		}
	}
	
	//Now drop it like it's hot
	$this->query("ALTER TABLE `collections` DROP `collector`");		
}
?>

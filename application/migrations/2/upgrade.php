<?php 
require_once 'EntitiesRelations.php';
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
	require_once 'Entity.php';
	//Export and add constraints
	$this->getTable('Entity')->export();
/*
		$this->query(
		"ALTER TABLE `entities` ADD UNIQUE `person` (`last_name` ( 75 ) , `first_name` ( 50 ) , `email` ( 75 ) , `middle_name` ( 30 ) ) ;
		ALTER TABLE `entities` ADD UNIQUE `institution` ( `institution` ( 255 ) ); ");
*/	
		
	//Create the anonymous entity
	$e = new Entity;
	$e->first_name = 'Anonymous';
	$e->inheritance_id = 1;
	$e->save();
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
			require_once 'Person.php';
			$p = new Person;
		
			$fields = array('first_name','last_name','email','institution');
			
			foreach ($fields as $k => $v) {
				$values[$v] = !empty($u[$v]) ? $u[$v] : null;
			}
			
			$user = $this->getTable('User')->find($u['id']);
			
			//If all the fields are missing, assign it to a new Person
			if(empty($u['first_name']) and empty($u['last_name']) and empty($u['email']) and empty($u['institution']) ) {
				
				$user->Entity->inheritance_id = 2;

			}
			//Otherwise we are going to need to search the DB for the pre-existing entry and if found, save that
			else {
				
				$toFind = array('first_name'=>$values['first_name'], 'last_name'=>$values['last_name'], 'email'=>$values['email']);

				$person = $this->getTable('Person')->findUniqueOrNew($toFind);
				
				if(!empty($values['institution'])) {
					$inst = $this->getTable('Institution')->findUniqueOrNew(array('institution'=>$u["institution"]));
					$inst->save();
					$person->parent_id = $inst->id;
				}

				$person->save();
				$user->entity_id = $person->id;	
			}
			$user->save();	
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
	$this->buildTable('EntitiesRelations');
}

//Next step is to convert all the user_id fields on the Item to EntitiesRelations relationships
if($this->tableHasColumn('Item', 'user_id')) {
	$this->buildTable('EntityRelationships');
	
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
			COLLECTION_RELATION_INHERITANCE_ID." as inheritance_id
		FROM collections c
		");
		
	foreach ($colls as $k => $coll) {
		if(!empty($coll['entity'])) {
			$entity = new Entity;
			$entity->first_name = $coll['entity'];
			$entity->inheritance_id = COLLECTION_RELATION_INHERITANCE_ID;
			$entity->dumpSave();
			
			$sql = "INSERT INTO entities_relations 
						(entity_id, relation_id, inheritance_id, relationship_id, time)
					VALUES (?, ?, ?, ?, NOW())";
					
			$this->query($sql, array($entity->id, $coll['relation_id'], $coll['inheritance_id'], 4));
		}
	}
	
	//Now drop it like it's hot
	$this->query("ALTER TABLE `collections` DROP `collector`");		
}
?>

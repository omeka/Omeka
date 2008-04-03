INSERT INTO `entities` ( `id`, `first_name`, `last_name`, `email`, `type`, `institution`)
VALUES
(1, 'Joe', 'Schmoe', 'joe@schmoe.hobo', 'Person', null),
(2, null, null, 'chnm@gmu.edu', 'Institution', 'Center for History and New Media');


INSERT INTO `users` ( `id` , `username` , `password`, `active` , `role`, `entity_id`)
VALUES 
(1, 'User1', SHA1( 'test' ) ,'1', 'super', 1),
(2, 'User2', SHA1( 'test' ) , '1', 'researcher', 2);


INSERT INTO items 
	(id, title) 
VALUES 
	(1, 'First Item'), 
	(2, 'Second Item'),
	(3, 'Third Item');

INSERT INTO tags 
	( `id` , `name` ) 
VALUES 
	( 1 , 'Tag1'), 
	( 2 , 'Tag2');

INSERT INTO taggings 
	(id, relation_id, tag_id, entity_id, type, time) 
VALUES 
	(1, 1, 1, 1, "Item", "1984-04-10 12:00:00"),
	(2, 1, 1, 1, "Exhibit", "1997-04-10 12:00:00"),
	(3, 1, 2, 1, "Exhibit", "1999-12-31 11:59:59"),
	(4, 1, 1, 2, "Exhibit", "2006-05-31 12:00:00"),
	(5, 2, 1, 1, "Item", "1990-01-01 12:00:00");

INSERT INTO `entities_relations` 
	( `id` , `relation_id` , `entity_id` , `relationship_id`, `type`, `time` )
VALUES 
	(1 , 1, 1, 1, "Item", '1984-04-10 12:00:00');

INSERT INTO `metatext` 
	( `id` , `item_id` , `metafield_id` , `text` )
VALUES 
	(1 , 1, 1, 'Metatext1');

INSERT INTO `collections` 
	( `id` , `name` , `description` , `public` , `featured` )
VALUES 
	(1 , 'Collection1', 'CollectionDescription1', '1', '1'), 
	(2 , 'Collection2', 'CollectionDescription2', '0', '0');

INSERT INTO `exhibits` 
	( `id` , `title` , `description` , `credits` , `featured` , `slug` , `theme` )
VALUES 
	(1 , 'Exhibit1', 'Description1', 'Credits1', '1', 'exhibit1', '');

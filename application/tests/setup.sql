INSERT INTO `users` ( `id` , `username` , `password` , `first_name` , `last_name` , `email` , `institution` , `active` , `role` )
VALUES (
NULL , 'User1', SHA1( 'test' ) , 'First1', 'Last1', 'user1@user1.com', 'CHNM', '1', 'super'
);

INSERT INTO `users` ( `id` , `username` , `password` , `first_name` , `last_name` , `email` , `institution` , `active` , `role` )
VALUES (
'2', 'User2', SHA1( 'test' ) , 'First2', 'Last2', 'user2@user2.com', 'CHNM', '1', 'researcher'
);

INSERT INTO items (id, title) VALUES (1, 'Item1');
INSERT INTO `tags` ( `id` , `name` ) VALUES ( 1 , 'Tag1'), (2 , 'Tag2');

INSERT INTO items_tags (item_id, tag_id, user_id) VALUES (1, 1, 1);

INSERT INTO items (id, title) VALUES (2, 'Item2');

INSERT INTO `items_favorites` ( `id` , `item_id` , `user_id` , `added` )
VALUES (
NULL , '1', '1', '2007-03-30 15:28:53'
);

INSERT INTO `metafields` ( `id` , `name` , `description` , `plugin_id` )
VALUES (
'1', 'Metafield1', 'Description of Metafield1', NULL
);

INSERT INTO `metatext` ( `id` , `item_id` , `metafield_id` , `text` )
VALUES (
NULL , '1', '1', 'Metatext1'
);

INSERT INTO `collections` ( `id` , `name` , `description` , `active` , `featured` , `collector` )
VALUES (
NULL , 'Collection1', 'CollectionDescription1', '1', '1', 'Collector1'
), (
NULL , 'Collection2', 'CollectionDescription2', '0', '0', 'Collector2'
);

--INSERT INTO `exhibits` ( `id` , `title` , `description` , `credits` , `featured` , `slug` , `theme` )
--VALUES (
--1 , 'Exhibit1', 'Description1', 'Credits1', '1', 'exhibit1', ''
--);

--INSERT INTO `exhibits_tags` ( `id` , `exhibit_id` , `tag_id` , `user_id` )
--VALUES (
--1 , '1', '1', '1'
--), (
--2 , '1', '2', '1'
--);


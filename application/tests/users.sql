INSERT INTO `users` ( `id` , `username` , `password` , `first_name` , `last_name` , `email` , `institution` , `active` , `group_id` )
VALUES (
NULL , 'Foobar', SHA1( 'test' ) , 'Foo', 'Bar', 'foo@bar.com', 'CHNM', '1', NULL
);

INSERT INTO `users` ( `id` , `username` , `password` , `first_name` , `last_name` , `email` , `institution` , `active` , `group_id` )
VALUES (
'2', 'bazfar', SHA1( 'test' ) , 'Baz', 'Far', 'baz@far.com', 'CHNM', '1', NULL
);

INSERT INTO items (id, title) VALUES (1, 'Item1');
INSERT INTO tags (id, name) VALUES (1, 'foo');
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

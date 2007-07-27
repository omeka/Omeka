INSERT INTO `types` ( `id` , `name` , `description` , `plugin_id` )
VALUES (
'50', 'Foobar', 'This is a random type.', NULL
);

INSERT INTO `items` ( `id` , `title` , `publisher` , `language` , `relation` , `spatial_coverage` , `rights` , `description` , `source` , `subject` , `creator` , `additional_creator` , `date` , `type_id` , `collection_id` , `contributor` , `rights_holder` , `provenance` , `citation` , `temporal_coverage_start` , `temporal_coverage_end` , `featured` , `public` )
VALUES 
(1 , 'Valid Item', '', 'eng', '', '', '', '', '', 'Random Subject', 'Jon Arbuckle', '', NULL , '50', NULL , '', '', '', '', NULL , NULL , '1', '1'), 
(2 , 'Invalid Item', '', '', '', '', '', '', '', '', '', '', NULL , '200', '10', '', '', '', '', NULL , NULL , '2', '0');
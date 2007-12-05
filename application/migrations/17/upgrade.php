<?php 
$db = get_db();
//Add the 'format' column to the items table
//drop the existing fulltext index
//rebuild the new index with the format field in it
$sql = " 
	ALTER TABLE $db->Item ADD COLUMN `format` text NOT NULL AFTER `additional_creator`;
	ALTER TABLE $db->Item DROP INDEX `search_all_idx`;
	ALTER TABLE $db->Item ADD FULLTEXT `search_all_idx` (`title`,`publisher`,`language`,`relation`,`spatial_coverage`,`rights`,`description`,`source`,`subject`,`creator`,`additional_creator`,`contributor`,`format`, `rights_holder`,`provenance`,`citation`);";
	
$db->execBlock($sql);


?>

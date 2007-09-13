<?php 
//Clean up the DB some

//Remove the 'routes' table

$this->query("DROP TABLE IF EXISTS `routes`;");


//Check if the plugins table still has those 3 fields we want to delete
$res = $this->query("DESCRIBE `plugins`");

$fields = pluck('Field', $res);

$toDrop = array('config', 'author', 'description');

foreach ($toDrop as $field) {
	if(in_array($field, $fields)) {
		//Drop that shit like a hot potato
		$this->query("ALTER TABLE `plugins` DROP `$field`");
	}
}

/*
	ALTER TABLE `plugins` DROP `description` ,
DROP `author` ,
DROP `config` ;");
*/	
?>

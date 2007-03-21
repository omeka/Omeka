<?php
$tableNames = array('Collection', 'File', 'Group', 'GroupsPermissions', 'Item', 'ItemsFavorites', 'ItemsTags', 'Metafield', 'Metatext', 'Permission', 'Plugin', 'Tag', 'Type', 'TypesMetafields', 'User');

foreach ($tableNames as $key => $name) {
	require_once '../application/models/'.$name.'.php';
	$record = new $name();
}

$searchableTables = array('items', 'collections');
	foreach ($searchableTables as $key => $table) {
		$sql = "CREATE TABLE `{$table}_fulltext` (`id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,`text` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,FULLTEXT (`text`)) ENGINE = MYISAM ;";
		Doctrine_Manager::connection()->execute($sql);
	}	

?>
